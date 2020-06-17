<?php

/**
 * Database Class
 *
 * Manages the database connection and streamlines queries
 
 * @author     Mark Solly <mark.solly@3plplus.com.au>
 */

class Database {
    //private $host = Config::get('DB_HOST');
	//private $user = Config::get('DB_USER');
	//private $db_name = Config::get('DB_NAME');
	//private $password = Config::get('DB_PASS');
	private $pdo = NULL;
	private $stmt;
    private static $database = null;

	private $options = array(
		//PDO::ATTR_PERSISTENT => true,
		//PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING ,
		PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
		PDO::ATTR_EMULATE_PREPARES => false
	);

    /**
     * This is the constructor for Database Object.
     *
     * @access private
     */
	private function __construct()
	{
		$dsn = 'mysql:dbname='.DB_NAME.';host='.DB_HOST;
		try
		{
			$this->pdo = new PDO($dsn, DB_USER, DB_PASS, $this->options);
		}
		catch (PDOException $e)
		{
			throw new Exception($this->displayError($e->getMessage()));
		}
	}//end constructor

    /**
     * This method for instantiating database object using the Singleton Design pattern.
     *
     * @access public
     * @static static method
     * @return Database Instantiate(if not already instantiated)
     *
     */
    public static function openConnection(){
        if(self::$database === null)
            self::$database = new Database();
        return self::$database;
    }

    /**
     * Closing the connection.
     *
     * @static static method
     * @access public
     * @see http://php.net/manual/en/pdo.connections.php
     */
    public static function closeConnection() {
        if(isset(self::$database)) {
            self::$database->pdo    =  null;
            self::$database->stmt   = null;
            self::$database         = null;
        }
    }

    /***************************************************************************
	* returns the exception
	*
	* @param  string $message
	* @param  string $sql
	* @return string
	*****************************************************************************/
	private function displayError($message , $sql = "", $params = array())
	{
		$exception = "";
		if(!empty($sql))
		{
			# Add the Raw SQL to the message
			$message .= "\r\nRaw SQL : "  . $sql;
		}
        $message .= "\r\nParameters : ";
        foreach($params as $key => $value)
        {
          $message .= "\r\n".$key." => ".$value;
        }
        $exception .= $message;

		return $exception;
	}

    /**********************************************
	*	submits a query
	*
	* @$query: string - a properly formed mysql query
	* @$params: array - parameters to bind to query
	* returns null
	***********************************************/
	public function query($query, $params = array())
    {
        try
		{
			//Prepare query
            if(!$this->pdo)
            {
                $dsn = 'mysql:dbname='.$this->db_name.';host='.$this->host.'';
                $this->pdo = new PDO($dsn, $this->user, $this->password, $this->options);
			    $this->connection = true;
            }
			$this->stmt = $this->pdo->prepare($query);
			//Bind parameters
			if(!empty($params))
			{
				$this->bindArrayValue($params);
			}
			//Execute query
			$this->stmt->execute();
			return true;
		}
		catch(PDOException $e)
		{
		    throw new Exception($this->displayError($e->getMessage(), $query, $params));
		}
    }

    /**********************************************
	*	queries a single row
	*
	* @$query: string - a properly formed mysql query
	* @$params: array - parameters to bind to query
	* returns associative array of data
	***********************************************/
	public function &queryRow($query, $params = array())
    {
        $this->query($query, $params);
        $rows = $this->stmt->fetch(PDO::FETCH_ASSOC);
        return $rows;
    }

    /**********************************************
	*	queries a single row by the id
	*
	* @$table: string - the name of the table
	* @$id: int - the id of the row
	* returns associative array of data
	***********************************************/
	public function &queryByID($table, $id)
    {
        $query = "SELECT * FROM $table WHERE id = $id";
        $rows = $this->queryRow($query);
        return $rows;
    }

	/**********************************************
	*	returns a query's result
	*
	* @$query: string - a properly formed mysql query
	* @$params: array - parameters to bind to query
	* returns multi-dimensional associative array of data
	***********************************************/
	public function &queryData($query, $params = array())
    {
       $this->query($query, $params);
       $rows = $this->stmt->fetchAll(PDO::FETCH_ASSOC);
       return $rows;
    }

	/**********************************************
	*	returns the value of a field (gets the id by default)
	*
	* @$table: 	string - name of table
	* @$cond: 	array  - field=>value
	* returns id if row exists, false if not
	***********************************************/
	public function queryValue($table, $cond, $field = 'id')
    {
        $where = "WHERE ";
		$c = 1;
		$v = "a";
		$params = array();
		foreach($cond as $sfield => $value)
		{
			$where .= "$sfield = :{$v}";
			if($c < count($cond))
			{
				$where .= " AND ";
			}
			$params[$v] = $value;
			++$c;
			++$v;
		}
		$row = $this->queryRow("SELECT `$field` FROM $table $where", $params);
		if($row)
		{
			$id = $row[$field];
		}
		else
		{
			$id = false;
		}
        return $id;

    }

	/**********************************************
	*	returns the first id of the rows where the field matches the given string
	*
	* @$table: 	string - name of table
	* @$field: 	string - name of field to match against
    * @$value:  string - the value to match against
	* returns id if row exists, false if not
	***********************************************/
	public function queryIdByFieldString($table, $field, $value)
    {
		$row = $this->queryRow("SELECT id FROM $table WHERE $field LIKE :value LIMIT 1", array('value' => $value));
		if(count($row))
		{
			$id = $row['id'];
		}
		else
		{
			$id = false;
		}
		return $id;

    }

    /**********************************************
	*	returns the first id of the rows where the field matches the given number
	*
	* @$table: 	string - name of table
	* @$field: 	string - name of field to match against
    * @$value:  number - the value to match against
	* returns id if row exists, false if not
	***********************************************/
	public function queryIdByFieldNumber($table, $field, $value)
    {
		$row = $this->queryRow("SELECT id FROM $table WHERE $field = :value LIMIT 1", array('value' => $value));
		if(count($row))
		{
			$id = $row['id'];
		}
		else
		{
			$id = false;
		}
		return $id;

    }

	/**********************************************
	*	returns the number of rows that satify the condition
	*
	* @$table: 	string - name of table
	* @$cond: 	array  - field=>value
	* returns id if row exists, false if not
	***********************************************/
	public function countData($table, $cond)
    {
        $where = "WHERE ";
		$c = 1;
		$v = "a";
		$params = array();
		foreach($cond as $sfield => $value)
		{
			$where .= "`$sfield` = :{$v}";
			if($c < count($cond))
			{
				$where .= " AND ";
			}
			$params[$v] = $value;
			++$c;
			++$v;
		}
		$row = $this->queryRow("SELECT count(*) AS count FROM $table $where", $params);
		return $row['count'];

    }

	/***************************************************
	**	Updates given field in a given table
	*
	* @$table: 	string - the table to be updated
	* @field: 	string - the field to be updated
	* @$value:	string/number/boolean/etc - the new value to be inserted
	* @$id:		integer - unique id of row to be updated
	* returns int - number of affected rows
	****************************************************/
   function updateDatabaseField($table, $field, $value, $id, $idfield = "id")
   {
		$params = array(
			'field'	=>	$value,
			'id'	=>	$id
		);
		//print_r($params); die();
		$this->query("UPDATE `$table` SET `$field` = :field WHERE `$idfield` = :id", $params);
		return $this->stmt->rowCount();
   }

	/***************************************************
	**	Updates several fields in a given table
	*
	* @$table: 	string 	- the table to be updated
	* @$values:	array 	- the values to be updated (field=>value)
	* @$id:		integer	- unique id of row to be updated
	* returns int - number of affected rows
	****************************************************/
   function updateDatabaseFields($table, $values, $id, $idfield = 'id')
   {
		  $q = "UPDATE $table SET ";
		  $c = 1;
		  $v = "a";
		  $params = array();
		  foreach($values as $field => $value)
		  {
			  $q .= "`$field` = :{$v}";
			  if($c < count($values))
			  {
				  $q .= ", ";
			  }
			  $params[$v] = $value;
			  ++$c;
			  ++$v;
		  }
		  $q .= " WHERE `$idfield` = :id";
		  $params['id'] = $id;
		  $this->query($q, $params);
		  return $this->stmt->rowCount();
   }


	/***************************************************
	**	Generates an insert query
	*
	* @$tablename: 	string 	- the table to insert values into
	* @$values:		array 	- the values to be inserted (field=>value)
	* returns unique id of row that has been inserted
	****************************************************/
	public function insertQuery($tablename, $values)
    {
		  $params = array();
		  $fields = array();
		  $place_holders = array();
		  foreach($values as $field => $value)
		  {
			  $fields[] = $field;
			  $params[$field] = $value;
			  $place_holders[] = ":".$field;
		  }
		  //$this->beginTransaction();
          //echo "INSERT INTO $tablename (`".implode("`,`",$fields)."`) VALUES (".implode(",",$place_holders).")";
          //echo "<pre>",print_r($params),"</pre>";
          //die();
          $this->query("INSERT INTO $tablename (`".implode("`,`",$fields)."`) VALUES (".implode(",",$place_holders).")", $params );
          $id = $this->pdo->lastInsertId();
		  if($id <= 0)
          {
    		  echo 'Error occurred:'.implode(":",$this->pdo->errorInfo()). "SQL: INSERT INTO $tablename (`".implode("`,`",$fields)."`) VALUES (".implode(",",$place_holders)."), ",print_r($params, true);
              die();
          }
          return $id;
    }
	/********************************************************
	**	Deletes entries
	*
	* @$tablename:	string	- the table to deletye values from
	* @value:		mixed	- the value to match against
    * @field        mixed   - the field to match against (default is id)
	* returns int - number of deleted rows
	**********************************************************/
	public function deleteQuery($tablename, $value, $field = 'id')
	{
		$this->query("DELETE FROM $tablename WHERE `$field` = :value", array("value" => $value));
		return $this->stmt->rowCount();
	}
	/*********************************************************************
	**	Determines if a value is already stored
	*
	* @$tablename:	string	- the table to search in
	* @value:		string/number/boolean/etc - the new value to be checked
	* @$field:		string - the name of the field to look in
	* returns boolean (true if found)
	************************************************************************/
	public function fieldValueTaken($tablename, $value, $field)
	{
		$rows = $this->queryData("SELECT `$field` FROM $tablename WHERE $field = :field", array('field' => $value));
		return (count($rows) > 0);
	}

	/*********************************************************************
	**	Returns the last insert id
	*
	* returns integer
	************************************************************************/
	public function getInsertID()
	{
		return $this->pdo->lastInsertId();;
	}

    /*********************************************************************
	**	Transaction helper functions
	*	NOT REQUIRED ON MYISAM TABLES
	* returns boolean (true on success)
	************************************************************************/

	private function beginTransaction()
	{
		return $this->pdo->beginTransaction();
	}

	private function endTransaction()
	{
		return $this->pdo->commit();
	}

	private function cancelTransaction()
	{
		return $this->pdo->rollBack();
	}

	/*********************************************************************
	**	Binds values from array to the prepared statement
	*
	* @$array:		array - field => value
	* returns void
	************************************************************************/
	private function bindArrayValue($array)
	{
		foreach($array as $key => $value)
		{
			if(is_int($value))
			{
				$this->stmt->bindValue(":$key", $value, PDO::PARAM_INT);
			}
			elseif(is_bool($value))
			{
				$this->stmt->bindValue(":$key", $value, PDO::PARAM_BOOL);
			}
			elseif(is_null($value))
			{
				$this->stmt->bindValue(":$key", NULL, PDO::PARAM_NULL); // why can't I just use $value here?!?
			}
			else
			{
				$this->stmt->bindValue(":$key", $value, PDO::PARAM_STR);
			}
		}
	}

    /**
     * Returns the number of rows affected by the last SQL statement
     * "If the last SQL statement executed by the associated PDOStatement was a SELECT statement, some databases may return the number of rows returned by that statement"
     *
     * @access public
     * @see http://php.net/manual/en/pdostatement.rowcount.php
     */
    public function countRows()
    {
        return $this->stmt->rowCount();
    }
}
