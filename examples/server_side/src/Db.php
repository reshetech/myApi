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
	 * The on duplicate part of the query.
	 *
	 * @var mixed
	 */ 	
	protected $onDuplicateString = null;
	
	/**
	 * The on duplicate array add parameters to the binding part of the query.
	 *
	 * @var array
	 */ 	
	protected $onDuplicateArray = array();

    /**
     * The insert array.
	 *
	 * @var array
     */
    protected $insertArray = array();	
		
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
	protected $limit    = array(0,40);
	
	/**
	 * The page number in the pagination.
	 *
	 * @var int
	 */ 
	protected $pageNumber = 0;
	
	/**
	 * The number of records per page in the pagination.
	 *
	 * @var int
	 */ 
	protected $perPage    = 40;
	
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
	 * The number of records returned from the query.
	 *
	 * @var int
	 */
	protected $rowCount = 0;
	
	/**
	 * The views object.
	 *
	 * @var Views
	 */
	protected $views;


    /**
	 * If we haven't created the connection, we'll create it.
	 *
	 * @return mixed
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
	 * Set the insert string for the query.
	 *
	 * @param  array  $insert
	 * @return array
	 */
	public function setInsert($insert)
	{	
		if($this->isValidInsert($insert))
			return $this->insertArray=$insert;
	}
	
	
    /**
	 * Set on duplicate key string for the query.
	 *
	 * @param string $str
	 * @param array  $arr
	 * @return string
	 */
	public function setOnDuplicateKey($str,$arr)
	{	
		$countArray = count($arr);
		
		$buildStr = " ON DUPLICATE KEY {$str} ";
		
		
		$i=0;
		foreach($arr as $item)
		{
			$item1 = trim($arr[$i][1]);
			
			if(preg_match('/^(-|\+)[0-9]+/',$item1))
			{
			    $buildStr .= " {$arr[$i][0]} = {$arr[$i][0]} {$arr[$i][1]} ";
		    }
			else
			{
			    $this->onDuplicateArray[][2] = $arr[$i][1];
				
				$buildStr .= " {$arr[$i][0]} = ? ";
			}
			
			if($i < $countArray-1)
			    $buildStr .= ",";
				
			$i++;
		}
		
		$this->onDuplicateString = $buildStr;
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
	 * Set the limit parameters for the current query.
	 *
	 * @param  int  $int1
	 * @param  int  $int2
	 */
	public function setLimit($int1,$int2)
	{				
		$arr = array((int)$int1,(int)$int2);
		
		$i=0;
		foreach($arr as $item)
		{
		    if($i==0 && $item<0) return false;
			
			if($i==1 && $item<1) return false;
			
			if(!is_int($item)) return false;

			$i++;
		}
		
        $this->limit = $arr;
	}
	
	
	/**
	 * Set the limit parameters for the pagination.
	 *
	 * @param mixed $num
	 * @param mixed $str
	 */
	public function setPagination($num=null,$str=null)
	{
		if(!$num && !$str)
		{
			if(!isset($this->pageNumber) || !isset($this->perPage)) return false;
			
			$arr = array($this->pageNumber, $this->perPage);
				
			if(!$this->isValidLimit($arr)) return false;
				
			$pageNumber = $this->pageNumber;
			
			$pageNumberMinus1 = $pageNumber-1;
			
			$perPage    = $this->perPage;

			
			return $this->setLimit($pageNumberMinus1*$perPage, $perPage);
		}
		
		
		if($str == 'num')
			return $this->perPage    = $num;
			
		
		if($str == 'page')
			return $this->pageNumber = $num;
	}
	

    /**
	 * Add pagination information to the results.
	 *
	 * @return mixed
	 */
	protected function paginationResult()
	{
	    if($this->pageNumber < 1) return false;
		

		$num         = $this->perPage;
		
		$currentPage = $this->pageNumber;
		
		$minPage     = 1;
		
		$maxPage     = ceil($this->rowCount/$num);
		
		$prevPage    = ($currentPage-1<$minPage)? false : $currentPage-1;
		
		$nextPage    = ($currentPage+1>$maxPage)? false : $currentPage+1;
		
		
		if($currentPage > $maxPage || $currentPage < $minPage) return false;
		
		
		$class = new \StdClass();
		
		$class -> page     = $currentPage." of ".$maxPage;
		
		$class -> results  = $num." of ".$this->rowCount;
		
		if($prevPage)
		    $class -> previous = 'num='.$num.'&page='.$prevPage;
			
		if($nextPage)
		    $class -> next     = 'num='.$num.'&page='.$nextPage;
		
		
		return $class;
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
	 * @param  array $arr
	 * @return bool
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
	 * Set the sql query string for the select command.
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
			
		//$sql .= " LIMIT {$this->limit[0]} ,{$this->limit[1]} ";

		return $this->sql = $sql;
    }
	
	
	/**
	 * Set the sql query string for the insert command.
	 *
	 * @return string
	 */
	protected function insert()
    {
		$insertArray = $this->insertArray;	
		
		$fields = array();
		$values = array();
		
		foreach($insertArray as $item)
		{
		    $fields[] = $item[0];
			$valPlaceHolders[] = '?';
		}
		
		$fields = "(".implode(', ',$fields).")";
		$valPlaceHolders = "(".implode(', ',$valPlaceHolders).")";
		
        $sql  = "INSERT INTO {$this->tableName} {$fields} "; 
        $sql .= " VALUES {$valPlaceHolders} ";
		

		if($this->onDuplicateString)
			$sql .= $this->onDuplicateString;
			
		return $this->sql = $sql;
    }

    
	/**
	 * Execute the current query.
	 *
	 * @return mixed
	 */
    protected function execute()
    {
		$insert = false;

		
		if(!empty($this->insertArray)) 
		    $insert = true;
		
		if($insert)
		    $this->insert();
		else
		    $this->select();			
			
			
		if(!empty($this->errors)) return false;

		
		// Prepare the query.
		$query = $this->dbh->prepare($this->sql); 

		
		// Bind the values.
		$arr = array();
		if($insert)
		{
     		$arr = $this->insertArray;

			if(!empty($this->onDuplicateArray))
			    $arr = array_merge($arr, $this->onDuplicateArray);

			$this->insertArray = array();
		}
        else
		{
			$arr = $this->whereArray;
			$this->whereArray = array();
		}

		$i = 1;
		foreach($arr as $item)
		{
			$query->bindValue($i,$item[2]);
			$i++;
		}

		
		// Execute the query.
        $query->execute();

        $results = $query->fetchAll(PDO::FETCH_OBJ);
		
		
        if($query -> rowCount() > 0)
		{
			$this->rowCount = $count = $query -> rowCount();
			
			if(!$insert)
			{
				$start = $this->limit[0];
								
			    $len   = $this->limit[1];

				$results=array_slice($results, $start, $len);
				
                
				return $this->results=$results;
			}
			else
			{
				return $this->results=$count;
			}
        }
        
		
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
	 * Turn a flat array into a multidimensional array.
	 *
	 * @param  array $arr
	 * @return array $arr
	 */
	protected function flatArrayToMulti($arr)
	{
	    if($this->isFlatArray($arr))
            return array(0=>$arr);

        return $arr;		
	}
	
	
	/**
	 * Check if the limit array for the query is valid.
	 *
	 * @param  array $arr
	 * @return bool
	 */
	private function isValidLimit($arr)
	{
		if(!is_array($arr) || empty($arr) || count($arr)<>2) return false;
		
		foreach($arr as $item)
		{
		    if(!is_integer($item) || $item<0) return false;
		}
		
		return true;
	}
	
	
	/**
	 * Check if an array is flat.
	 *
	 * @param  array $arr
	 * @return bool
	 */
	protected function isFlatArray($arr)
	{
	    if (count($arr) == count($arr, COUNT_RECURSIVE))
            return true;
        
		return false;
	}
	

	/**
	 * Check if the where array for the query is valid.
	 *
	 * @param  array $arr
	 * @return bool
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
	 * Check if the insert array for the query is valid.
	 *
	 * @param  array $arr
	 * @return bool
	 */
	private function isValidInsert($arr)
	{
		if(!is_array($arr) || empty($arr)) return false;	
		
		foreach($arr as $item)
		{
			$numItems = count($item);				
					
			if(empty($item) || $numItems <> 3) return false;
			
			
			if(!is_string($item[0])) return false;
			
			
			$allowedOperators = array('=');
	
			if(trim($item[1]) != '=') return false;
			
			
			if(!is_string($item[2]) && !is_integer($item[2])) return false;
	    }

		return true;
	}
	
	
	/**
	 * Check if the orderBy array for the query is valid.
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
	 * @param  array $where
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
	 * @param  string $str
	 * @return mixed
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
	 * Attach pdo constants to the binding step.
	 *
	 * @param  string $str
	 * @return constants
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
