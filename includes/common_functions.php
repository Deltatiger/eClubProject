<?php

/*
 * This contains all the common functions.
 */

function generateRandString($length)	{
    //This generates a random string of $length charecters long
    $randomString = '';
    $range = str_split('abcdefghijklmnopqrstuvwxyz1234567890<>?:"{}!@#$%^&*()_+', '1');
    for($i = 0; $i < $length; $i++)	{
        $randomString .= array_rand($range);
    }
    return $randomString;
}

function registerUser($username, $password, $email)  {
    global $db;
    /*
     * @desc : This function is used to register a new user and login in the current User.
     *          We check 2 things namely username repetition and email repetition
     */
    $usernameClean = strtolower(trim($username));
    $emailClean = strtolower(trim($email));
    $sql = "SELECT `user_name`, `user_email`, `user_id` FROM `{$db->name()}`.`dbms_user` WHERE LOWER(`user_name`) = '{$usernameClean}' || LOWER(`user_email`) = '{$emailClean}'";
    $query = $db->query($sql);
    if (mysql_num_rows($query) > 1)     {
        //We already a row with this things.
        mysql_free_result($query);
        return false;
    } else {
        //We seem to be free of the user. We proceed to register him.
        $usernameClean = trim($username);
        $emailClean = trim($email);
        //This is the crypt for hashing the password.
        $passwordHash = sha1($password);
        $sql = "INSERT INTO `{$db->name()}`.`dbms_user`(`user_name`,`user_pass`,`user_email`) VALUES ('{$usernameClean}','{$passwordHash}','{$emailClean}')";
        $query = $db->query($sql);
    }
    return true;
}   
?>
