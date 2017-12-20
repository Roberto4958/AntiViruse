<?php
session_start();

//dont hardcode numbers (no magic varibles)
//check that you sanatize all variables 

//questions: 
// should we validate user input on login?
// what should we dod on die
// what is the best way to implment cookie with sessions? 
//ask about session time

$error = "";

require_once "loginInfo.php";
require_once "helperScripts.php";

//check if user has already been authenticated
if(!SessionIsValid()) header('Location: logIn');

//determin if user logged out 
if($_POST){
    if(isset($_POST['logout'])){
        endSession();
        header('Location: logIn');
    }
}

//determin if user is admin
$selection = '';
if($_SESSION['role'] === 'admin'){
    $selection = '<select id="AddOrStore" name="typeOfAction" size="1">
	               <option value="check" selected>check for viruses</option>
	               <option value="store">Store into databas</option>
            </select>
            <input style= "width:40%;margin:10px auto;" type="text" id="virus-title" class="form-control" placeholder="Name of virus" name = "virusName">';
}

//set up the form accordingly 
$form = " $selection
            <input type='file' name='fileToUpload' id='fileToUpload'>
            <input id='submit' type='submit'>";

if ( $_FILES){    

    
    if($_FILES["fileToUpload"]["tmp_name"]){
        
        $content = file_get_contents($_FILES["fileToUpload"]["tmp_name"]);
        $asciiInput = stringToAscii($content);
        
        if(isset($_POST['typeOfAction'])){
            if($_POST['typeOfAction'] === 'store'){
                
                if(isset($_POST['virusName'])) $virusName = fixString($_POST['virusName']);
                
                $fail = validate_virusName($virusName);
                if($fail == ""){
                    $ascci = getFirstTwentyBytes($asciiInput);
                    $form = addvirusToDB($ascci, $virusName, $hn, $un, $pw, $db);
                }
                else $error = "<p class='error'style='text-align:center; width:100%'>$fail</p>";
            } 
            else $form = checkFile($asciiInput, $hn, $un, $pw, $db);
        }      
        else $form = checkFile($asciiInput, $hn, $un, $pw, $db);
    } 
    else  $error = "<p class='error' style='text-align:center; width:100%'>Please choose a file</p>";
}

//@Desc: checkes if file is a virus
//@Param: $asciiInput: String of ASCCI code each code seperated by a space
function checkFile($asciiInput, $hn, $un, $pw, $db){
    $viruses = getAscciFromDB($hn, $un, $pw, $db);
    if(isVirus($asciiInput, $viruses)){
        return '<br> <h1 class="virus">This File contains dangerous contant</h1>';
    }
    else{
        return '<br> <h1 class="safe">We did not detect any dangerous contant</h1>';
    }
}

//@desc: Gets the the first 20 ascci code from input
//@return: String of 20 containing ASCCI code seperated by space
//@param: $input: a string of assci code seperated by spaces
function getFirstTwentyBytes($input){
     
    $ascciArray = explode(' ', $input);
    
    $str = $ascciArray[0];
    for($i=1; $i < 20 ; $i++){
        $str .= ' ' . $ascciArray[$i]; 
    }
    return $str;
}

//@Desc: turns a string into ascci code and seperates each code by a space
//@Desc: a string of ascii code 
//@param: $input: a string
function stringToAscii($input){
    $ascci = '';
    for($i=0; $i < strlen($input); $i++){
        $s =  ord(substr($input, $i, $i+1));
        $ascci .= $s . ' ';
    }
    return $ascci;
}

//@Desc: scans $content to see if there any matches from $viruses (array)
//@Param: $content String containing ascci code example: 110 30 84 30 
//@Param: $viruses and array of accscii strings
function  isVirus($content, $viruses){
    
    //loop through all the db data
    for($i=0; $i < count($viruses); $i++){ 
        $v = explode(" ", $viruses[$i]);
        $cont = explode(" ", $content);
        $tempCont = $cont;
    
        //loop through all the char of the file
        while( count($tempCont) > count($v)) {
       
            //compar all the char from 
            for($j=0; $j <= count($v); $j++){
                if($j === count($v)) return true; 
                if($v[$j] !== $tempCont[$j]) break;
            }
            array_shift($tempCont);   
        }
        
    }
    return false;
}

//@Desc: addes input into the database
//@param: $input: ascii string seperated by spaces
function addvirusToDB($input,$virusName, $hn, $un, $pw, $db){
    
    $conn = new mysqli($hn, $un, $pw, $db);
    if ($conn->connect_error) return '<h1 class="virus"> Unable to add connection to db</h1>'; 
    $sql = "insert into viruses(title, content) value(?, ?)"; 
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $virusName, $input); 
    $stmt->execute();
    $stmt->close();
    $conn->close();
    return '<h1 class="safe">Added data successfully</h1>'; 
}


function getAscciFromDB($hn, $un, $pw, $db){
    $viruses = array();
    
    $conn = new mysqli($hn, $un, $pw, $db);
    if ($conn->connect_error){
        die($conn->connect_error);
        return;
    } 
    $query = "select content from viruses";
    $result = $conn->query($query);

     if(!$result){
         die($conn->connect_error);
         return;
     }
    $rows = $result->num_rows;    
    
    for($i=0; $i < $rows; $i++){
        $result->data_seek($i);
        $obj = $result->fetch_array(MYSQLI_ASSOC);
        $viruses[] = $obj['content'];
    }
    
    $result->close();
    $conn->close();
    return $viruses;
}

echo <<<_END

<html>
    <head>  
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
        <link rel="stylesheet" href="CSS/style.css">
        <link rel="stylesheet" href="CSS/home-style.css">
        <script src='JS/jquery-3.0.0.min.js'></script>
        <script src='JS/logic.js'> </script>
        <script src='JS/authenticate.js'></script>
    </head>
<body>
    
<nav class="navbar navbar-light bg-faded">
  <form class="form-inline" method="post">
    <button class="btn" id="logout-btn" type="submit" name='logout'>Log out</button>
  </form>
</nav>


<div class="container">
    <div class="row">
    
        <form action="index.php" method="post" onsubmit='return virusNameValidate(this)' enctype="multipart/form-data">
            <img id="scanIcon" src="img/scan.png" alt="page" >
            $error
            $form
<!--            <h1 class="safe">Safe</h1>-->
        </form>
    </div> 
    </div>
</body>
</html>


_END;
?>