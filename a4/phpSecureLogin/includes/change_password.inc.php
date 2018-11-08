<?php

/* 
 * Copyright (C) 2013 peter
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

include_once 'db_connect.php';
include_once 'psl-config.php';
include_once 'functions.php';

$error_msg = "";

if (isset($_POST['oldp'], $_POST['email'], $_POST['p'], $_POST['security_answer'])) {
    // Sanitize and validate the data passed in
    $forgot_password = filter_input(INPUT_POST, 'security_answer', FILTER_SANITIZE_STRING);   
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $email = filter_var($email, FILTER_VALIDATE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Not a valid email
        $error_msg .= '<p class="error">The email address you entered is not valid</p>';
    }
    
    $password = filter_input(INPUT_POST, 'p', FILTER_SANITIZE_STRING);
    if (strlen($password) != 128) {
        // The hashed pwd should be 128 characters long.
        // If it's not, something really odd has happened
        $error_msg .= '<p class="error">Invalid password configuration.</p>';
    }
    
    $oldp = filter_input(INPUT_POST, 'oldp', FILTER_SANITIZE_STRING);
    
    if (strlen($oldp) != 128) {
        // The hashed pwd should be 128 characters long.
        // If it's not, something really odd has happened
        $error_msg .= '<p class="error">Invalid password configuration.</p>';
    }
    
    if (login($email, $oldp, $mysqli) == false && checkans($email,$forgot_password,$mysqli)==false)
    {
        // Neither old password is correct nor the security answer
        $error_msg .= '<p class="error">Invalid password, and answer.</p>';
    }

    if (empty($error_msg)) {
        // Create a random salt
        $random_salt = hash('sha512', uniqid(openssl_random_pseudo_bytes(16), TRUE));

        // Create salted password 
        $password = hash('sha512', $password . $random_salt);
        // UPDATE `members` SET `contact_number` = '0759 253 542' WHERE `membership_number` = 1;
        // Update the new password into the database 
        // UPDATE members SET (password,salt) VALUES (?,?) where `email` = ?;
        if ($update_stmt = $mysqli->prepare("UPDATE members SET password=?,salt=? where email = ?")) {
            $update_stmt->bind_param('sss', $password, $random_salt, $email);
            // Execute the prepared query.
            if (! $update_stmt->execute()) {
                header('Location: ../error.php?err=Change password failure: UPDATE PASSWORD');
                exit();
            }
        }
        header('Location: ./change_password_success.php');
        exit();
    }
}
