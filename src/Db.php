<?php
namespace Reshetech\MyApi;
use \PDO;

require 'config/const.php';


class Db
{
    /**
	 * The active PDO connection.
	 *
	 * @var dbh
	 */	
    protected $dbh;
	
	/**
	 * The query string.
	 *
	 * @var string
	 */	
    protected $sql;
	
	/**
	 * The results returned from the query.
	 *
	 * @var results
	 */
    protected $results; 
	
	/**
	 * The name of the table to be queried.
	 *
	 * @var string
	 */
	protected $tableName;
	
	/**
	 * The fields to query in the select query.
	 *
	 * @var array
	 */
	protected $fields   = array();
	
	/**
	 * The where part of the query.
	 *
	 * @var string
	 */    
	protected $whereString    = " 1 ";

    /**
     * The where array.
	 *
	 * @var array
     */
    protected $whereArray = array();	 
	
	/**
	 * The order by part of the query.
	 *
	 * @var array
	 */ 
	protected $orderBy  = array(); 
	
	/**
	 * The limit part of the query.
	 *
	 * @var array
	 */ 
	protected $limit    = array(0,10);
	
	/**
	 * The aliased names of the fields.
	 *
	 * @var array
	 */
	protected $fieldsAliases = array();
	
	/**
	 * The errors array.
	 *
	 * @var array
	 */ 
    protected $errors   = array();
	
	/**
	 * The views object.
	 *
	 * @var Views
	 */
	protected $views;
	

    /**
	 * If we haven't created the connection, we'll create it.
	 *
	 * @return void
	 */
    public function __construct()
    {
		$this->views=new Views();
		
		if(!$this->dbh) 
		    return $this->connector();
    }
	

	/**
	 * Set the table name for the current query.
	 *
	 * @param  string  $tableName
	 * @return mixed
	 */
	public function setTableName($tableName)
	{
		if(!is_string($tableName)) 
		    return $this->errors[]="Unacceptable table name. Table name should be a string.";
		
		return $this->tableName=$tableName;
	}
	
	
	/**
	 * Set the fields for the current query.
	 *
	 * @param  array  $fields
	 * @return array
	 */
	public function setFields($fields)
	{
		if(!is_array($fields)) 
		    return $this->errors[]="Fields should be inputted as array of fields.";
		
		if(count($fields)<1)
		    return $this->errors[]="There should be at least one field in the fields array";
			
		return $this->fields = $fields;
	}
	
	
	/**
	 * Get the results of the current query.
	 *
	 * @return mixed
	 */
    public function getResults()
    {
       $this->execute();
	        
       if(!empty($this->errors)) return false;
       
       return $this->results;
    }
    
    
	/**
	 * Get the class errors.
	 *
	 * @return mixed
	 */
    public function getErrors()
    {
        if(empty($this->errors)) return false;
			
        $errors = $this->errors;
		
		$build = "<ul>";
        
		foreach($errors as $error)
		{
			$build .= "<li>{$error}</li>";
		}
		$build .= "</ul>";
		
		return $build;
    }
	
	
	/**
	 * Set the where string for the query.
	 *
	 * @param  array  $where
	 * @return string
	 */
	public function setWhere($where)
	{	
		if($this->isValidWhere($where))
		{	
			$where = $this->flatArrayToMulti($where);

			return $this->whereArray=$where;
		}
	}
	
	
	/**
	 * Set the orderBy array for the current query.
	 *
	 * @param  array  $orderBy
	 * @return array
	 */
	public function setOrderBy($orderBy)
	{	
		if($this->isValidOrderBy($orderBy))
		    return $this->orderBy = $orderBy;
	}
	
	
	/**
	 * Set the limit array for the current query.
	 *
	 * @param  array  $limit
	 * @return array
	 */
	public function setLimit($limit)
	{			
		if($this->isValidLimit($limit))
		    $this->limit = $limit;
	}
	
	
	/**
	 * Set an alias to the fields names.
	 *
	 * @param  array $arr
	 * @return mixed
	 */
	public function setFieldsAlias(array $arr)
	{
	    $arr = Utilis::cleanArray($arr);

		if($this->isValidFieldsAliases($arr))
		    return $this->fieldsAliases = $arr;
		
		$this->errors[]='The number of items in your fields aliases array does not match the number of items in your fields array.';
	}
	
	
	/**
	 * Return the array of aliased fields if exists, otherwise the original array of fields.
	 * 
	 * @return array
	 */
	public function getAliassedFields()
	{
	    if(!empty($this->fieldsAliases))
		    return $this->fieldsAliases;
	
	    return $this->fields;
	}	
	
	
	/**
	 * Check if the fields aliases array is valid.
	 * 
	 * @return boolean
	 */
	protected function isValidFieldsAliases($arr)
	{
		if(!is_array($arr)) return false;
		
		if(count($arr)<>count($this->fields)) return false;
		
		foreach($arr as $item)
		    if(trim($item) == '') return false;
		
		return true;
	}
	
	
	/**
	 * Set the sql query string.
	 *
	 * @return string
	 */
	protected function select()
    {
        $fieldsStr = $this->selectedFields();
		
		$this->buildWhere($this->whereArray);
        
        $sql  = "SELECT {$fieldsStr} "; 
        $sql .= " FROM {$this->tableName} ";	
		$sql .= " WHERE {$this->whereString} ";
		
		if(!empty($this->orderBy))
			$sql .= " ORDER BY {$this->orderBy[0]} {$this->orderBy[1]} ";
			
		$sql .= " LIMIT {$this->limit[0]} ,{$this->limit[1]} ";

		return $this->sql = $sql;
    }
    
    
	/**
	 * Execute the current query.
	 *
	 * @return mixed
	 */
    protected function execute()
    {
        $this->select();
		
		if(!empty($this->errors)) return false;
		
		$query = $this->dbh->prepare($this->sql); 
		
		$i     = 1;
		$arr   = $this->whereArray;
		foreach($arr as $item)
		{		
			$query->bindValue($i,$item[2]);
			
			$i++;
		}

        $query->execute();
		
        $results = $query->fetchAll(PDO::FETCH_OBJ);

        if($query -> rowCount() > 0)
            return $this->results=$results;
        
        $this->errors[]='No results.';
    }
	
	
	/**
	 * Create the database connection.
	 *
	 * @return mixed
	 */
	private function connector()
	{
		try {
	        return $this->dbh = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME,
	                DB_USER,DB_PASS,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));

		} catch (PDOException $e) {
		    	
			echo $e->getMessage(); exit;
	            
	        $this->errors[]='Unable to connect to the database.';
	    }
	}
	
	
	/**
	 * Checks if the limit array for the query is valid.
	 *
	 * @return bool
	 */
	private function isValidLimit($arr)
	{
		if(!is_array($arr)) return false;
		
		if(empty($arr) || count($arr) <>2) return false;
		
		if(!is_integer($arr[0]) || !is_integer($arr[1])) return false;
		
		return true;
	}

	
	/**
	 * Turns a flat array into a multidimensional array.
	 *
	 * @param  array $arr
	 * @return array
	 */
	protected function flatArrayToMulti($arr)
	{
	    if($this->isFlatArray($arr))
            return array(0=>$arr);

        return $arr;		
	}
	
	
	/**
	 * Checks if an array is flat.
	 *
	 * @param  array $arr
	 * @return boolean
	 */
	protected function isFlatArray($arr)
	{
	    if (count($arr) == count($arr, COUNT_RECURSIVE))
            return true;
        
		return false;
	}
	

	/**
	 * Checks if the where array for the query is valid.
	 *
	 * @return boolean
	 */
	private function isValidWhere($arr)
	{
		if(!is_array($arr) || empty($arr)) return false;
		
		$arr = $this->flatArrayToMulti($arr);	
		
		foreach($arr as $item)
		{
			$numItems = count($item);				
					
			if(empty($item) || $numItems < 3 || $numItems > 4) return false;
			
			
			if(!is_string($item[0])) return false;
			
			
			$allowedOperators = array('=','>','<','<=','>=');
	
			if(!in_array(trim($item[1]),$allowedOperators)) return false;
			
	
			if(!is_string($item[2]) && !is_integer($item[2])) return false;
			
			
			$allowedConditions = array('and','or','');
			
			if(isset($item[3]) && !in_array(strtolower(trim($item[3])),$allowedConditions)) return false;
	    }
		
		return true;
	}
	
	
	/**
	 * Checks if the orderBy array for the query is valid.
	 *
	 * @param  array $arr
	 * @return bool
	 */
	private function isValidOrderBy($arr)
	{
		if(!is_array($arr)) return false;
		
		if(empty($arr) || count($arr) <>2) return false;	
			
		$allowedSortings = array('asc','desc');	
			
		if(!is_string($arr[0])) return false;
		
		if(!in_array(strtolower($arr[1]),$allowedSortings)) return false;
		
		return true;
	}
    
    
	/**
	 * Create string of fields out of the fields array.
	 *
	 * @return string
	 */
    private function selectedFields()
	{
		$fields = $this->fields;
		
		return implode(",", $fields);
	}
	
	
	/**
	 * Create where string out of the where array.
	 *
	 * @return string
	 */
	private function buildWhere($where)
	{
		$count = count($where);
		
		$build=' ';
			
		for($i=0; $i<$count; $i++)
		{
			$where0 = $this->aliasToOriginalFieldName($where[$i][0]);
			
			$build .= "`".$where0."`".$where[$i][1]."?";
			
			if($i<$count-1) 
			{
				$build .= (isset($where[$i][3]))? " {$where[$i][3]} " : " AND ";
			}
		}
	
	    return $this->whereString=$build.' ';
	}
	
	
	/**
	 * Replace the alias field name, if exist, with the table field name.
	 *
	 * @var string $str
	 * @param string
	 **/
	private function aliasToOriginalFieldName($str)
	{
	    if(!empty($this->fieldsAliases))
		{
			$position = (array_search($str, $this->fieldsAliases));
			
			return $this->fields[$position];
		}
		
		return $str;
	}
	
	
	/**
	 * Attaches pdo constants to the binding step.
	 *
	 * @param  string $str
	 * @return pdo constants
	 */
	private function param($str)
	{
		switch($str)
		{
		  case 'int':
		    $param = PDO::PARAM_INT;
		    break;  
			 
		  case 'str':
		    $param = PDO::PARAM_STR;
		    break;
			
		  default:
		    $param = PDO::PARAM_STR;
		    break;
		}

		return $param;
	}
}
