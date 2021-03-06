<?php

/*
 * This has the connection to the mysql database.
 */
class DB    {
    private $con;
    private $dbName;
  
    //This is the constructor
    function __construct() {
        $this->con = mysql_connect('localhost','root','');
        if (mysql_errno($this->con))  {
            die('Could not Connect to the DataBase.');
        }
        
        mysql_select_db('eclub', $this->con);
        $this->dbName = 'eclub';
    }
    
    function __destruct()   {
        mysql_close($this->con);
    }
    
    public function escapeString($str)  {
        return mysql_real_escape_string($str);
    }
    
    public function freeResults($result)   {
        if(is_a($result, 'mysql_result'))  {
            mysql_free_result($result);
        }
    }
    
    public function name() {
        return $this->dbName;
    }
	
	public function table($name)	{
		/*
		 * This is used to return the Name of the actual name of the table given the table name
		 */
		switch($name)	{
			case 'user':
				return 'e_user';
			case 'session':
				return 'e_session';
			case 'item':
				return 'e_item';
			case 'require':
				return 'e_require';
			case 'inventory':
				return 'e_inventory';
			case 'loan':
				return 'e_loan';
			case 'auction':
				return 'e_auction';
			case 'production':
				return 'e_production';
			case 'config':
				return 'e_config';
		}
	}
    
    public function reconnect() {
        //This closes the current connection and reconnects. Avoiding Resource unavailable error for now.
        mysql_close($this->con);
        $this->con = mysql_connect('localhost', 'root', '');
        if(mysql_errno())  {
            die('Could not Connect to the DataBase.');
        }
        mysql_select_db('e_club', $this->con);
    }
    
    public function result($query)  {
        $result =  mysql_fetch_object($query);
        return $result;
    }
    
    public function numRows($query)  {
        return mysql_num_rows($query);
    }
    
    public function query($sqlQuery) {
        /**
         * This function is used to perform mysql_query() (or) mysqli_query() on the open Connection.
         */
        $query = mysql_query($sqlQuery, $this->con) or die('SQL Error : '. mysql_error($this->con).'<br />'.$sqlQuery);
        return $query;
    }
    
}
?>
