var UserNameLegthMin = 5;
var passwordLengthMin = 5;

function CreateAccountValidate(form){
    fail = validateUsername(form.username.value);
    fail += validatePassword(form.password.value);
    fail += validateEmail(form.email.value);
		
    if (fail == "") return true; 
    else { alert(fail); return false }

}

function virusNameValidate(form){
    
    if (form.typeOfAction.value == 'check') return true;
    fail = validateVirusname(form.virusName.value);
    if (fail == "") return true; 
    else { alert(fail); return false }
}


function validateVirusname(input){
    if (input == "") return "No Virus Name was entered.\n"
	else if (input.length < UserNameLegthMin)
		return "virus Name  must be at least "+UserNameLegthMin+" characters.\n"
	else if (/[^a-zA-Z0-9_-]/.test(input))
		return "virus Name may only have letters, numbers, -, or _ <br>.\n"
	return ""
}

function validateUsername(username){
    if (username == "") return "No Username was entered.\n"
	else if (username.length < UserNameLegthMin)
		return "Usernames must be at least "+UserNameLegthMin+" characters.\n"
	else if (/[^a-zA-Z0-9_-]/.test(username))
		return "username may only have letters, numbers, -, or _ <br>.\n"
	return ""
}

function validateEmail(email){
    if (email == "") return "No Email was entered.\n"
	else if (!((email.indexOf(".") > 0) && (email.indexOf("@") > 0)) || /[^a-zA-Z0-9.@_-]/.test(email))
		return "The Email address is invalid.\n"
	return ""

}

function validatePassword(password){
    if (password == "") return "No Password was entered.\n"
	else if (password.length < passwordLengthMin)
		return "Passwords must be at least "+passwordLengthMin+" characters.\n"
	else if (!/[a-z]/.test(password) || ! /[A-Z]/.test(password) ||!/[0-9]/.test(password))
		return "Passwords must have lowercase, uppercase, and a number.\n"
	return ""

}

  
