/* 
 * Copyright (C) 2013 peredur.net
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
function formhash(form, password) {
    // Create a new element input, this will be our hashed password field. 
    var p = document.createElement("input");

    // Add the new element to our form. 
    form.appendChild(p);
    p.name = "p";
    p.type = "hidden";
    p.value = hex_sha512(password.value);

    // Make sure the plaintext password doesn't get sent. 
    password.value = "";

    // Finally submit the form. 
    form.submit();
}

function regformhash(form, uid, email, password, conf,forgot) {
    // Check each field has a value
    if (uid.value == '' || email.value == '' || password.value == '' || conf.value == '' || forgot.value == '' ) {
        alert('You must provide all the requested details. Please try again');
        return false;
    }
    
    // Check the username
    re = /^\w+$/; 
    if(!re.test(form.username.value)) { 
        alert("Username must contain only letters, numbers and underscores. Please try again"); 
        form.username.focus();
        return false; 
    }
    re = /^\w+$/; 
    if(!re.test(forgot.value)) { 
        alert("Security answer must contain only letters, numbers and underscores. Please try again"); 
        form.forgot_password.focus();
        return false; 
    }
    // Check that the password is sufficiently long (min 6 chars)
    // The check is duplicated below, but this is included to give more
    // specific guidance to the user
    if (password.value.length < 6) {
        alert('Passwords must be at least 6 characters long.  Please try again');
        form.password.focus();
        return false;
    }
    
    // At least one number, one lowercase and one uppercase letter 
    // At least six characters 
    var re = /(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}/; 
    if (!re.test(password.value)) {
        alert('Passwords must contain at least one number, one lowercase and one uppercase letter.  Please try again');
        return false;
    }
    
    // Check password and confirmation are the same
    if (password.value != conf.value) {
        alert('Your password and confirmation do not match. Please try again');
        form.password.focus();
        return false;
    }
    var common_passwords =["123456","123456789","qwerty","12345678","111111","1234567890","1234567","password","123123","987654321","qwertyuiop","mynoob","123321","666666","18atcskd2w","7777777","1q2w3e4r","654321","555555","3rjs1la7qe","google","1q2w3e4r5t","123qwe","zxcvbnm","1q2w3e"];

    //Check levenshteinDistance
	var length = common_passwords.length;
    for(var i = 0; i < length; i++) {
        if(levenshteinDistance(password.value,common_passwords[i]) < 5)
        {
			alert('Your password is very weak, please use a stronger password');
		    return false;
		}    
	}

    // Create a new element input, this will be our hashed password field. 
    var p = document.createElement("input");

    // Add the new element to our form. 
    form.appendChild(p);
    p.name = "p";
    p.type = "hidden";
    p.value = hex_sha512(password.value);

    // Make sure the plaintext password doesn't get sent. 
    password.value = "";
    conf.value = "";

    // Finally submit the form. 
    form.submit();
    return true;
}

function change_password(form, oldpassword, email, password, conf,forgot) {
    // Check each field has a value
    if ((oldpassword.value == '' && forgot.value == '') || email.value == '' || password.value == '' || conf.value == ''  ) {
        alert('You must provide all the requested details. Please try again');
        return false;
    }
    re = /^\w+$/; 
    if(!re.test(forgot.value) && forgot.value!='') { 
        alert("Security answer must contain only letters, numbers and underscores. Please try again"); 
        form.forgot_password.focus();
        return false; 
    }
    // Check that the password is sufficiently long (min 6 chars)
    // The check is duplicated below, but this is included to give more
    // specific guidance to the user
    if (password.value.length < 6) {
        alert('Passwords must be at least 6 characters long.  Please try again');
        form.password.focus();
        return false;
    }
    
    
    // At least one number, one lowercase and one uppercase letter 
    // At least six characters 
    var re = /(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}/; 
    if (!re.test(password.value)) {
        alert('Passwords must contain at least one number, one lowercase and one uppercase letter.  Please try again');
        return false;
    }
    
    // Check password and confirmation are the same
    if (password.value != conf.value) {
        alert('Your password and confirmation do not match. Please try again');
        form.password.focus();
        return false;
    }
    var common_passwords =["123456","123456789","qwerty","12345678","111111","1234567890","1234567","password","123123","987654321","qwertyuiop","mynoob","123321","666666","18atcskd2w","7777777","1q2w3e4r","654321","555555","3rjs1la7qe","google","1q2w3e4r5t","123qwe","zxcvbnm","1q2w3e"];

    //Check levenshteinDistance
	var length = common_passwords.length;
    for(var i = 0; i < length; i++) {
        if(levenshteinDistance(password.value,common_passwords[i]) < 5)
        {
			alert('Your password is very weak, please use a stronger password');
		    return false;
		}    
	}

    // Create a new element input, this will be our hashed password field. 
    var p = document.createElement("input");
    var oldp = document.createElement("input");
    // Add the new element to our form. 
    form.appendChild(p);
    p.name = "p";
    p.type = "hidden";
    p.value = hex_sha512(password.value);

    // Make sure the plaintext password doesn't get sent. 
    password.value = "";
    conf.value = "";

    // Add the new element to our form. 
    form.appendChild(oldp);
    oldp.name = "oldp";
    oldp.type = "hidden";
    oldp.value = hex_sha512(oldpassword.value);

    // Make sure the plaintext oldpassword doesn't get sent. 
    oldpassword.value = "";

    // Finally submit the form. 
    form.submit();
    return true;
}
