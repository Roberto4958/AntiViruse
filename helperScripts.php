<?php
//--- Helper methods ---///

function randomString($len){
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $len; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function fixString($input){
    if(get_magic_quotes_gpc()) $input = stripslashes($input);
    return htmlentities($input);
}

function stringisSafe($input){
    if(strlen($input) < 1) return false;
    return true;
}

//--- Session handeler ---///
$SESSION_TIME = 86400 * 30; // one day

function startSession($token, $id, $role){
    $seesionTime = time() + $GLOBALS['SESSION_TIME']; 
    $cookie_name = 'token';
    $cookie_value = $token;
    
    
    setcookie($cookie_name, $cookie_value, $seesionTime, "/");

    $_SESSION['role'] = $role; 
    $_SESSION['id'] = $id;
    $_SESSION['check'] = hash('ripemd128', $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'] . $token);
    //echo $_SERVER['REMOTE_ADDR'] .'<br>'. $_SERVER['HTTP_USER_AGENT'] . '<br>' . $token .'<br>'. $_SESSION['check'];                                                                      
}

function endSession(){
    $_SESSION = array();
    setcookie(session_name(), '', time() - 2592000,  "/");
    session_destroy();
}

function SessionIsValid(){    
    //echo $_SERVER['REMOTE_ADDR'] .'<br>'. $_SERVER['HTTP_USER_AGENT'] . '<br>' . $_COOKIE['token'] .'<br>'. $_SESSION['check'];
    $hash = hash('ripemd128', $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'] . $_COOKIE['token']);
    if($hash !== $_SESSION['check']){
        endSession();
        return false; 
    } 
    return true; 
}

///------validation---///

//global varibales
$VIRUS_NAME_MIN_LENGTH = 5; 
$USER_MIN_LENGTH = 5; 
$PASWORD_MIN_LENGTH = 5;


function validate_virusName($virusname){
    $virusnameMinLength = $GLOBALS['VIRUS_NAME_MIN_LENGTH'];

    if($virusname =="") 
        return "No username was entered <br>"; 
    if(strlen($virusname) < $virusnameMinLength) 
        return "User name must be atleast $virusnameMinLength characters long <br>"; 
    if(preg_match("/[^a-zA-Z0-9_-]/", $virusname))
        return "username may only have letters, numbers, -, or _ <br>";
    return ""; 
}

function validate_userName($username){
    $usernameMinLength = $GLOBALS['USER_MIN_LENGTH'];

    if($username =="") 
        return "No username was entered <br>"; 
    if(strlen($username) < $usernameMinLength) 
        return "User name must be atleast $usernameMinLength characters long <br>"; 
    if(preg_match("/[^a-zA-Z0-9_-]/", $username))
        return "username may only have letters, numbers, -, or _ <br>";
    return ""; 
}

function validate_password($password){
    $passwordMinLength = $GLOBALS['PASWORD_MIN_LENGTH'];
    
    if($password =="") 
        return "No password was entered <br>"; 
    if(strlen($password) < $passwordMinLength) 
        return "Password must be atleast $passwordMinLength characters long <br>";        
    if(!preg_match("/[a-z]/", $password) || !preg_match("/[A-Z]/", $password) ||!preg_match("/[0-9]/", $password))
        return "Passwords must have lowercase, uppercase, and a number<br>";
    return "";
}

function validate_email($email){
    if($email == "")
        return "No username was entered <br>";
    if( !((strpos($email, ".") > 0) && (strpos($email, "@") > 0)) && preg_match("/[a-z]/", $email)) 
        return "The Email address is invalid <br>";
}

?>
