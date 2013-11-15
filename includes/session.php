<?php

/**
 * Description of session
 *  This class is used to maintain the session of the user. This has the constructor which enables the init checking
 *
 * @author DeltaTiger
 */


class Session {
    
    public function __construct() {
        //This is the main constructor method.
        $currentTime = time();
        $currentIp = $_SERVER['REMOTE_ADDR'];
        $currentBrowser = $_SERVER['HTTP_USER_AGENT'];
        
        //We also call the global $db object.
        global  $db;
        
        if ( isset($_SESSION['session_id']))    {
            //The user has an active session. We have to validate the session.
            $sessionId = $_SESSION['session_id'];
            $sql = "SELECT `session_last_active`, `session_create_ip`, `session_browser` FROM `{$db->name()}`.`{$db->table('session')}` WHERE `session_id` = '{$sessionId}'";
            $query = $db->query($sql);
            if ( $db->numRows($query) == 0)    {
                //This means that the session does not exist. Unset it an make a new session.
                $db->freeResults($query);
                $this->createNewSession();
            } else {
                //He has a valid session in the Db. Check if it is still within 5 min activity and let him pass.
                $result = $db->result($query);
                if ( $currentTime - intval($result->session_last_active) > 300 /* 5 Mins */)    {
                    //This means that the current Session is over due. Remove it and recreate it.
                    $this->createNewSession();
                } else {
                    //We check the final stage ie) the ip and browser.
                    if ( $result->session_create_ip != $currentIp || $result->session_browser != $currentBrowser)   {
                        //The session and IP dont match. Create a new Session.
                        $this->createNewSession();
                    } else {
                        //This means that the session is valid. Update the time and let him pass.
                        $db->freeResults($query);
                        $sql = "UPDATE `{$db->name()}`.`{$db->table('session')}` SET `session_last_active` = '{$currentTime}' WHERE `session_id` = '{$sessionId}'";
                        if ( ! $db->query($sql) )   {
                            $db->freeResults($query);
                            die('Session Update Failed. Contact Admin.');
                        }
                    }
                }
                $db->freeResults($query);
            }
        } else {
            //This means we create a new session ID.
            if ( isset($_COOKIE['cookie_id']))  {
                //We seem to have a cookie. Validate it.
            } else {
                //Neither a Cookie nor a session. We create a new session.
                $this->createNewSession();
            }
        }
    }
    
    public function isLoggedIn()    {
        if(isset($_SESSION['session_id']))  {
            //We have a valid session.
            global $db;
            //We get the login status and return it.
            $sessionId = $_SESSION['session_id'];
            $sql = "SELECT `session_login_stat` FROM `{$db->name()}`.`{$db->table('session')}` WHERE `session_id` = '{$sessionId}'";
            $query = $db->query($sql);
            if ($db->numRows($query) > 0) {
                //Return the valid session.
                $result = mysql_fetch_object($query);
                $db->freeResults($query);
                return ($result->session_login_stat == '1');
            } else {
                $db->freeResults($query);
                return False;
            }
        }
        return False;
    }
    
    public function login($username, $password) {
        // This is used to check the login credentials.
        if ($this->isLoggedIn())    {
            return false;
        } else {
            //We log the user in.
            global $db;
            $usernameClean = strtolower(trim($username));
            $passwordHash = sha1(trim($password));
            $sql = "SELECT `user_id` FROM `{$db->name()}`.`{$db->table('user')}` WHERE LOWER(`user_name`) = '{$usernameClean}' AND `user_pass` = '{$passwordHash}'";
            $query = $db->query($sql);
            if (mysql_num_rows($query) > 0) {
                //We proceed to log the user in.
                $result = mysql_fetch_object($query);
                $db->freeResults($query);
                $sql = "UPDATE `{$db->name()}`.`{$db->table('session')}` SET `session_login_stat` = '1' , `session_user_id` = '{$result->user_id}' WHERE `session_id` = '{$_SESSION['session_id']}'";
                $query = $db->query($sql);
                $db->freeResults($query);
                // Now we also have to update the basket from . TODO : This.
                $this->uBasket->setBasketUser();
                return true;
            } else {
                return false;
            }
        }
    }
    
    public function getUserId() {
        // This gets the user id from the session.
        global $db;
        if ( isset($_SESSION['session_id']))    {
            //We have a session. We return the user_id if he has a login status 1.
            $sql = "SELECT `session_user_id`, `session_login_stat` FROM `{$db->name()}`.`{$db->table('session')}` WHERE `session_id` = '{$_SESSION['session_id']}'";
            $query = $db->query($sql);
            if ( $db->numRows($query) > 0)    {
                //Match Found.
                $result = $db->result($query);
                $db->freeResults($query);
                if ( $result->session_login_stat == '1')    {
                    return $result->session_user_id;
                } else {
                    return 0;
                }
            } else {
                //We dont have any rows. We unset the session.
                $db->freeResults($query);
                unset($_SESSION['session_id']);
                return 0;
            }
        } else {
            //We dont have a session. We return 0.
            return 0;
        }
    }
    
    public function getUserName()   {
        global $db;
        if ( isset($_SESSION['session_id']))    {
            $sql = "SELECT `user_name` FROM `{$db->name()}`.`{$db->table('user')}` , `{$db->name()}`.`dbms_session` WHERE `{$db->table('user')}`.`user_id` = `{$db->table('session')}`.`session_user_id` AND `{$db->table('session')}`.`session_id` = '{$_SESSION['session_id']}' AND `{$db->table('session')}`.`session_login_stat` = '1'";
            $query = $db->query($sql);
            $result = $db->result($query);
            $db->freeResults($query);
            if ( $db->numRows($query) > 0)  {
                return $result->user_name;
            } else {
                return 'Guest';
            }
        }
    }
    
    private function createNewSession() {
        //This is used to make a new session.
        $newSessionId = $this->createNewSessionId();
        $currentTime = time();
        $currentIp = $_SERVER['REMOTE_ADDR'];
        $currentBrowser = $_SERVER['HTTP_USER_AGENT'];
        global $db;
		if(isset($_SESSION['session_id']))	{
			//There is an active Session.
			$sql = "SELECT COUNT(`session_user_id`) as user_count FROM `{$db->name()}`.`{$db->table('session')}` WHERE `session_id` = '{$_SESSION['session_id']}'";
			$query = $db->query($sql);
			$result = $db->result($query);
			if($result->user_count == 0)	{
				$sql = "INSERT INTO `{$db->name()}`.`{$db->table('session')}` VALUES ('{$newSessionId}', NULL, '{$currentTime}', '{$currentTime}', '{$currentIp}', '{$currentBrowser}', '0')";
			} else {
				$sql = "UPDATE `{$db->name()}`.`{$db->table('session')}` SET `session_create_ip` = '{$currentIp}', `session_browser` = '{$currentBrowser}', `session_login_stat` ='0' , `session_user_id` = 'NULL', `session_create_time` = '{$currentTime}', `session_last_active` = '{$currentTime}' , `session_id` = '{$newSessionId}' WHERE `session_id` = '{$_SESSION['session_id']}'";
			}
			$db->query($sql);
		} else {
			$sql = "INSERT INTO `{$db->name()}`.`{$db->table('session')}` VALUES ('{$newSessionId}', NULL, '{$currentTime}', '{$currentTime}', '{$currentIp}', '{$currentBrowser}', '0')";
			$db->query($sql);
		}
		
    }
    
    public function getUserNameFromSession()    {
        if (!$this->isLoggedIn())   {
            return false;
        }
        //Now we link the tables
        global $db;
        $sessionId = $_SESSION['session_id'];
        $sql = "SELECT `user_name` FROM `{$db->name()}`.`{$db->table('user')}`, `{$db->name()}`.`{$db->table('session')}` WHERE `{$db->table('session')}`.`session_user_id` = `{$db->table('user')}`.`user_id` AND `session_id` = '{$sessionId}'";
        $query = $db->query($sql);
        if ($db->numRows($query) > 0)     {
            $result = $db->result($query);
            $userName = $result->user_name;
            $db->freeResults($query);
            return $userName;
        } else {
            $db->freeResults($query);
            return False;
        }
    }
    
    private function createNewSessionId()   {
        //This function is used to create a new id.
        $stringToCrpyt = generateRandString(6);
        $encrpytedString = sha1($stringToCrpyt);

        global $db;

        $sql = "SELECT `session_id` FROM `{$db->name()}`.`{$db->table('session')}` WHERE `session_id` = '{$encrpytedString}'";
        $query = $db->query($sql);

        while($db->numRows($query) > 0) {
            $stringToCrypt = generateRandString(6);
            $db->freeResults($query);
            $encrpytedString = sha1($stringToCrypt);
            $query = $db->query($sql);
        }
        $db->freeResults($query);
        return $encrpytedString;
    }
}

?>