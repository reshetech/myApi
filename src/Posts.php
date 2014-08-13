<?php
namespace Reshetech\MyApi;

class Posts extends Db
{
	/**
	 * Maximum nubmer of returned results.
	 *
	 * @var integer
	 */	
	protected $maxResults = 1000;
	
	/**
	 * Minimum nubmer of returned results. Should not be under 1.
	 *
	 * @var integer
	 */	
    protected $minResults = 1;
	
	/**
	 * Nubmer of returned results.
	 *
	 * @var integer
	 */
	protected $num;
	
	
	/**
	 * Set the nubmer of returned results.
	 *
	 * @param  integer
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
	 * Return the query results.
	 *
	 * @return mixed
	 */
	public function getResults()
    {
        $this->execute();
	        
        if(!empty($this->errors)) return false;
       
        return array($this->tableName,$this->fields,$this->results);    
    }
	
	
	/**
	 * Checks if valid the number of returned results.
	 *
	 * @param  integer $num
	 * @return boolean
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
}
