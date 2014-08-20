<?php
namespace Reshetech\MyApi;

class Posts extends Db
{
	/**
	 * Maximum number of returned results.
	 *
	 * @var int
	 */	
	protected $maxResults = 1000;
	
	/**
	 * Minimum number of returned results. Should not be under 1.
	 *
	 * @var int
	 */	
    protected $minResults = 1;
	
	/**
	 * Nubmer of returned results.
	 *
	 * @var int
	 */
	protected $num;
	
	/**
	 * Replace the table name as the root name for the results in XML.
	 *
	 * @var string
	 */
    protected $tableNameAlias = '';	
	
	
	/**
	 * Set the values of necessary variables for this class.
	 *
	 * @param string $tableName
	 * @param array  $fields
	 * @param string $where
	 */
	public function create($tableName,$fields,$where)
	{
	    $this->setTableName($tableName);
		
	    $this->setFields($fields);
		
	    $this->prepareWhere($where);
	}
	
	
	/**
	 * Set the number of returned results.
	 *
	 * @param  int
	 * @return mixed
	 */
	public function setNum($num)
    {	        
		$num = (int)$num;
		
		if($num == 0 || $num == '') $num = 100;
        
        $isValidNum = $this->isValidNum($num);
		
		if(!$isValidNum)
		{
			return $this->errors[]="Number of results should be between: {$this->minResults} and {$this->maxResults}.";
		}
        
		$this -> num = $num;
		
		$limit=array(0,$num);
		
		return $this->setLimit($limit);
    }
	
	
	/**
	 * Set an alias to the table name.
	 *
	 * @param  string $str
	 * @return string
	 */
	public function setTableAlias($str)
	{
	    $str = trim($str);
		
		if(is_string($str) && $str != '')
		{
		    $this->tableNameAlias = Utilis::cleanString($str);
		}
	}
	
	
	/**
	 * Return the alias for the table name, if exists, otherwise the original table name.
	 * 
	 * @return string
	 */
	protected function getAliasedTableName()
	{
	    if($this->tableNameAlias !== '')
		    return $this->tableNameAlias;
	
	    return $this->tableName;
	}
	
	
	/**
	 * Return the query results.
	 *
	 * @return mixed
	 */
	public function get()
    {
        $this->execute();
	        
        if(!empty($this->errors))
		{
		    $this->views->notFound('No results found.')->getHeader();
			
			$errors = Utilis::arrayToString($this->errors);
			
			$this->views->writeToScreen($errors,true);
			
			return false;
		}
        else
		{
            $tableName = $this->getAliasedTableName();
			
			$fields    = $this->getAliassedFields();
			
			$results   = $this->transformFieldNames($this->results);
			
			return array($tableName,$fields,$results);
        }			
    }
	
	
	/**
	 * Check if the number of returned results is valid.
	 *
	 * @param  int $num
	 * @return bool
	 */
	private function isValidNum($num)
	{
		if($num > $this->maxResults || $num < $this->minResults) return false;
		
		return true;
	}
	
	
	/**
	 * Helper to explode the where string by operators like '=','>','<'.
	 *
	 * @param  string $str
	 * @param  string $by
	 * @return mixed
	 */
	protected function explodeBy($str,$by)
	{
	    $arr = array();
		
		if(strpos($str,$by) !== false)
		{
			$exploded = explode($by,$str);
			
			foreach($exploded as $item)
			{
			    $arr[] = $item;	
			}
			return $arr;
		}
		
		return $str;
	}
	
	
	/**
	 * Explode the where string by the operators '=','>','<','>=','<='.
	 *
	 * @param  string $str
	 * @param  string $junc
	 * @return array
	 */
    protected function explodeByOperator($str,$junc)
	{
	    $allowedOperators = array('\>=','\<=','\>','\<','=');
		
		$operator = '=';
		foreach($allowedOperators as $op)
		{
		    $opRegex = '/'.$op.'/';
		    if(preg_match($opRegex,$str))
            {
                $operator=preg_replace('/\\\/','',$op);
           	}		
		}
		
		$exploded = explode($operator,$str);
		
		return array($exploded[0],' '.$operator.' ',$exploded[1],$junc);
	}
	
	
	/**
	 * Explode the where string by the operators '+','|'.
	 *
	 * @param  string $str
	 * @return 
	 */
	public function prepareWhere($str)
	{
		$str = trim($str);
		
		if(!is_string($str)) return false;
		
		if($str == '') return false;
	
		$str = urldecode($str);

	    $arr = array();

		$arrayExplodedByAnd = $this->explodeBy($str,' ');
		
		if(is_array($arrayExplodedByAnd))
		{
		    foreach($arrayExplodedByAnd as $itemExplodedByAnd)
			{
			    $arr[] = $this->explodeBy($itemExplodedByAnd,'|');
			}
		}
		else
		{
		    $arr[] = $this->explodeBy($str,'|');
		}

		$where = array();

		foreach($arr as $andItem)
		{
			if(is_string($andItem)) 
            {
				$where[] = $this->explodeByOperator($andItem,'and');
            }
            else
            {
                foreach($andItem as $orItem)
				{
					$where[] = $this->explodeByOperator($orItem,'or');
				}
            }			
		}

	    return $this->setWhere($where);
	}
	
	
	/**
	 * Replace the fields names with the fields aliases names.
	 *
	 * @param  array $entries
	 * @return array
	 */
	protected function transformFieldNames($entries)
	{
		if(empty($this->fieldsAliases))
		    return $entries;
			
		$fieldsAliases    = $this->fieldsAliases;

        $transformedArray = array();
		
		$k=0;
		foreach($entries as $entry)
		{
			$object = new \StdClass;
			
			$i=0;
			
			foreach($entry as $origKey => $value)
			{
				$newKey = $fieldsAliases[$i];
				
				$object->$newKey = $value;
				
				$i++;
			}
			
			$transformedArray[$k] = $object;
			
			$k++;
		}
		return $transformedArray;
	}
}
