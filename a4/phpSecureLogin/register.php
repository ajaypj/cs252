<?php
/**
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
include_once 'includes/register.inc.php';
include_once 'includes/functions.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Secure Login: Registration Form</title>
        <script type="text/JavaScript" src="js/sha512.js"></script> 
        <script type="text/JavaScript" src="js/forms.js">
			import levenshteinDistance from "./levenshteinDistance.js";		
		</script>
        <script type="text/JavaScript" src="js/levenshteinDistance.js">
			export default function levenshteinDistance(a, b);		
		</script>		
        <link rel="stylesheet" href="styles/main.css" />
    </head>
    <body>
        <!-- Registration form to be output if the POST variables are not
        set or if the registration script caused an error. -->
        <h1>Register with us</h1>
        <?php
        if (!empty($error_msg)) {
            echo $error_msg;
        }
        ?>
        <ul>
            <li>Usernames may contain only digits, upper and lower case letters and underscores</li>
            <li>Emails must have a valid email format</li>
            <li>Passwords must be at least 6 characters long</li>
            <li>Passwords must contain
                <ul>
                    <li>At least one upper case letter (A..Z)</li>
                    <li>At least one lower case letter (a..z)</li>
                    <li>At least one number (0..9)</li>
                </ul>
            </li>
            <li>Your password and confirmation must match exactly</li>
        </ul>
        <form method="post" name="registration_form" id="registration_form" action="<?php echo esc_url($_SERVER['PHP_SELF']); ?>">
            Username: <input type='text' name='username' id='username' /><br>
            Email: <input type="text" name="email" id="email" /><br>
            Password: <input type="password"
                             name="password" 
                             id="password"/><br>
            Confirm password: <input type="password" 
                                     name="confirmpwd" 
                                     id="confirmpwd" />
            
            <progress max="100" value = "0" id="strength" name="strength" style="width:230px"></progress>
            
            <br>
            
            Security Question: 1st pet, school, or best friend(Case sensitive) 
                                    <input type="text" 
                                     name="forgot_password" 
                                     id="forgot_password" />
            <br>
            <input type="button" 
                   value="Register" 
                   onclick="return regformhash(this.form,
                                   this.form.username,
                                   this.form.email,
                                   this.form.password,
                                   this.form.confirmpwd,
                                   this.form.forgot_password);" /> 
        </form>
        <p>Return to the <a href="index.php">login page</a>.</p>
    </body>
    <!--SCRIPT FOR PROGRESS BAR-->
	<script type = "text/javascript">
		var pass = document.getElementById("registration_form").elements["password"];
        pass.addEventListener('keyup', function(){
            checkPassword(pass.value);
        });
        function checkPassword(password){
            // var strengthBar = document.getElementById("registration_form").elements["strength"];
            var strengthBar = document.getElementById("strength");           
            var minstrength = 100;
            var common_passwords =["123456","123456789","qwerty","12345678","111111","1234567890","1234567","password","123123","987654321","qwertyuiop","mynoob","123321","666666","18atcskd2w","7777777","1q2w3e4r","654321","555555","3rjs1la7qe","google","1q2w3e4r5t","123qwe","zxcvbnm","1q2w3e"];
            //Check levenshteinDistance
            var length = common_passwords.length;
            var temp = 100;
            for(var i = 0; i < length; i++) {
                temp = levenshteinDistance(password,common_passwords[i]);
                if(temp < minstrength)
                {
                    minstrength = temp;
                }    
            }
            var strength = 0;
            if (password.length<=6)
                strength = 0;
            else
                strength = minstrength;
            // console.log(strength);
            if (strength < 1)
                strengthBar.value = 0;
            else if(strength < 3)
                strengthBar.value = 20;
            else if(strength < 5)
                strengthBar.value = 40;
            else if(strength < 7)
                strengthBar.value = 60;
            else if(strength < 10)
                strengthBar.value = 80;
            else
                strengthBar.value = 100;
        }
	</script>
</html>
