<?php

/**
 * Description of session
 *  This class is used to main the session of the user. This has the constructor which enables the init checking
 *
 * @author DeltaTiger
 */


class Session {
    public $uBasket;
    
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
            $sql = "SELECT `session_last_active`, `session_create_ip`, `session_browser` FROM `{$db->name()}`.`dbms_session` WHERE `session_id` = '{$sessionId}'";
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
                        $sql = "UPDATE `{$db->name()}`.`dbms_session` SET `session_last_active` = '{$currentTime}' WHERE `session_id` = '{$sessionId}'";
                        if ( ! $db->query($sql) )   {
                            $db->freeResults($query);
                            die('Session Update Failed. Contact Admin.');
                        }
                        $sql = "SELECT `session_basket_id` FROM `{$db->name()}`.`dbms_session` WHERE `session_id` = '{$sessionId}'";
                        $query = $db->query($sql);
                        $result = $db->result($query);
                        $this->uBasket = new Basket($result->session_basket_id);
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
    
    public function isSeller()  {
        /**
         * PHP Custom Function.
         * This function is used to return the seller status of the currently logged in User.
         */
        global $db;
        if (!$this->isLoggedIn())   {
            return false;
        }
        //We can only do this for a user that is logged in.
        $sql = "SELECT `seller_approved` FROM `{$db->name()}`.`dbms_user`, `{$db->name()}`.`dbms_seller_info` WHERE `dbms_seller_info`.`seller_user_id` = `dbms_user`.`user_id` AND `dbms_user`.`user_id` = '{$this->getUserId()}'";
        $query = $db->query($sql);
        if ( $db->numRows($query) > 0)  {
            // This means that the user is a seller.
            $result = $db->result($query);
            if ($result->seller_approved  == 1) {
                $db->freeResults($query);
                return true;
            }
        }
        $db->freeResults($query);
        return false;
    }
    
    public function isLoggedIn()    {
        if(isset($_SESSION['session_id']))  {
            //We have a valid session.
            global $db;
            //We get the login status and return it.
            $sessionId = $_SESSION['session_id'];
            $sql = "SELECT `session_login_stat` FROM `{$db->name()}`.`dbms_session` WHERE `session_id` = '{$sessionId}'";
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
            $sql = "SELECT `user_id` FROM `{$db->name()}`.`dbms_user` WHERE LOWER(`user_name`) = '{$usernameClean}' AND `user_pass` = '{$passwordHash}'";
            $query = $db->query($sql);
            if (mysql_num_rows($query) > 0) {
                //We proceed to log the user in.
                $result = mysql_fetch_object($query);
                $db->freeResults($query);
                $sql = "UPDATE `{$db->name()}`.`dbms_session` SET `session_login_stat` = '1' , `session_user_id` = '{$result->user_id}' WHERE `session_id` = '{$_SESSION['session_id']}'";
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
            $sql = "SELECT `session_user_id`, `session_login_stat` FROM `{$db->name()}`.`dbms_session` WHERE `session_id` = '{$_SESSION['session_id']}'";
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
            $sql = "SELECT `user_name` FROM `{$db->name()}`.`dbms_user` , `{$db->name()}`.`dbms_session` WHERE `dbms_user`.`user_id` = `dbms_session`.`session_user_id` AND `dbms_session`.`session_id` = '{$_SESSION['session_id']}' AND `dbms_session`.`session_login_stat` = '1'";
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
        $newSesId = $this->createNewSessionId();
        $currentTime = time();
        $currentIp = $_SERVER['REMOTE_ADDR'];
        $currentBrowser = $_SERVER['HTTP_USER_AGENT'];
        $oldBasketId = 0;
        global $db;
        if (isset($_COOKIE['cookie_id']))   {
            //We have a cookie. We need to add the table first.
            //TODO : Setup the cookie table and do the required.
        } else {
            if ( isset($_SESSION['session_id']))    {
                // Since we have a session there must be a basket assosiated with it.
                // First we have to check if the current session was a logged in session.
                $sql = "SELECT `session_login_stat` FROM `{$db->name()}`.`dbms_session` WHERE `session_id` = '{$_SESSION['session_id']}'";
                $query = $db->query($sql);
                if ($db->numRows($query) <= 0)  {
                    //Invalid Session.
                    $this->uBasket = new Basket(-1);
                } else {
                    $result = $db->result($query);
                    if ( $result->session_login_stat == 1)  {
                        //We have a user who is logged in. We do not delete the basket. But we have to assign a new one to him.
                        $this->uBasket = new Basket(-1);
                    } else {
                        //This is a user who is not logged in. He will be using the same Basket as always.
                        $db->freeResults($query);
                        $sql = "SELECT `session_basket_id` FROM `{$db->name()}`.`dbms_session` WHERE `session_id` = '{$_SESSION['session_id']}'";
                        $query = $db->query($sql);
                        $result = $db->result($query);
                        $this->uBasket = new Basket($result->session_basket_id);
                    }
                }
                $db->freeResults($query);
                $sql = "DELETE FROM `{$db->name()}`.`dbms_session` WHERE `session_id` = '{$_SESSION['session_id']}'";
                $db->query($sql);
                $db->freeResults($query);
                /*
                //We have to delete this from the Db and make a new one.
                //First we check if the user had any items in his Basket.
                $sql = "SELECT COUNT(`basket_item_id`) as itemsInBasket 
                    FROM `{$db->name()}`.`dbms_session`, `{$db->name()}`.`dbms_basket_contains` 
                    WHERE `dbms_session`.`session_basket_id` = `dbms_basket_contains`.`basket_id` AND
                        `dbms_session`.`session_id` = '{$_SESSION['session_id']}'";
                $query = $db->query($sql);
                if (mysql_num_rows($query) > 0) {
                    //This means that the basket has some items. We have to preserve the Basket Id.
                    $sql = "SELECT `session_basket_id` FROM `{$db->name()}`.`dbms_session` WHERE `session_id` = '{$_SESSION['session_id']}'";
                    $query = $db->query($sql);
                    if ( mysql_num_rows($query) > 0 )   {
                        $result = mysql_fetch_object($query);
                        $oldBasketId = $result->session_basket_id;
                        $this->uBasket = new Basket($oldBasketId);
                    } else {
                        $this->uBasket = new Basket(-1);
                    }
                } else {
                    //We dont have enough items in the Basket. Better we delete it.
                    $sql = "DELETE FROM `{$db->name()}`.`dbms_basket` WHERE `basket_id` = (SELECT `session_basket_id` FROM `{$db->name()}`.`dbms_session` WHERE `session_id` = '{$_SESSION['session_id']}')";
                    $query = $db->query($sql);
                    $this->uBasket = new Basket(-1);
                }
                $sql = "DELETE FROM `{$db->name()}`.`dbms_session` WHERE `session_id` = '{$_SESSION['session_id']}'";
                $db->query($sql);
                unset($_SESSION['session_id']);
                 */
            } else {
                //No session seems to be available. We make a new Basket for the user and assign it to him.
                $this->uBasket = new Basket(-1);
            }
            $oldBasketId = $this->uBasket->getBasketId();
            $sql = "INSERT INTO `{$db->name()}`.`dbms_session` VALUES ('{$newSesId}', NULL, {$currentTime}, {$currentTime}, 0, '{$currentIp}', '{$currentBrowser}', 0, {$oldBasketId})";
            $query = $db->query($sql);
            if ( !$query )   {
                die('Session Creation Problem. Contact Admin.');
            }
            $_SESSION['session_id'] = $newSesId;
            $db->freeResults($query);
        }
    }
    
    public function getUserNameFromSession()    {
        if (!$this->isLoggedIn())   {
            return false;
        }
        //Now we link the tables
        global $db;
        $sessionId = $_SESSION['session_id'];
        $sql = "SELECT `user_name` FROM `{$db->name()}`.`dbms_user`, `{$db->name()}`.`dbms_session` WHERE `dbms_session`.`session_user_id` = `dbms_user`.`user_id` AND `session_id` = '{$sessionId}'";
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

        $sql = "SELECT `session_id` FROM `{$db->name()}`.`dbms_session` WHERE `session_id` = '{$encrpytedString}'";
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