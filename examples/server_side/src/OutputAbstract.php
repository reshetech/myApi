<?php
namespace Reshetech\MyApi;

abstract class OutputAbstract
{
	/**
	 * The name of the returned results according to the queried table in the database.
	 *
	 * @var string
	 */	
	protected $tableName; 
	
	/**
	 * Array of fields names for the output according to the fields in the queried database table.
	 *
	 * @var array
	 */
	protected $fields=array();

    /**
	 * The fields aliases.
	 *
	 * @var array
	 */
	protected $fieldsAliases = array();

	/**
	 * Replace the table name as the root name for the results in XML.
	 *
	 * @var array
	 */
    protected $tableNameAlias = '';	
		
	/**
	 * Array of results returned from the database query.
	 *
	 * @var array
	 */	
	protected $results;
	
	/**
	 * Array of results for the output.
	 *
	 * @var array
	 */
	protected $entreis=array();
	
	/**
	 * Number of records presented.
	 *
	 * @var integer
	 */
	protected $numOfEntries;
	
	/**
	 * The errors array.
	 *
	 * @var array
	 */ 
    protected $errors   = array();
	
    
	/**
	 * Distribute the data of the database query between the class' variables.
	 *
	 * @param  array
	 * @return mixed
	 */
	public function __construct(array $results)
    {
	    $this->tableName = $results[0];
			   
	    $this->fields    = $results[1];
		
		$this->results   = $results[2];
    }
	

	/**
	 * Set an alias to the table name.
	 *
	 * @param  string $str
	 * @return string
	 */
	public function setAliasedTableName($str)
	{
	    $str = trim($str);
		
		if(is_string($str) && $str != '')
		{
		    $this->tableNameAlias = Utilis::cleanString($str);
		}
	}
	
	
	/**
	 * Set an alias to the fields names.
	 *
	 * @param  array $arr
	 * @return mixed
	 */
	public function setAliasedFieldsNames(array $arr)
	{
	    $arr = Utilis::cleanArray($arr);

		if($this->isValidFieldsAliases($arr))
		    return $this->fieldsAliases = $arr;
		
		$this->errors[]='The number of items in your fields aliases array does not match the number of items in your fields array.';
	     
	}

	
	/**
	 * Turn the object returned from the database query into an array.
	 *
	 * @return array
	 */
    protected function objToArray()
    {
		$entreis=array();
				
        $numOfEntry = 0;
		$results=$this->results;
	    foreach($results as $result)
        {
           $fields=$this->fields;	

           foreach($fields as $field)
		   {
               $entreis[$numOfEntry][$field] = $result->$field;
		   }
           
			$numOfEntry++;
        }
		$this->numOfEntries=$numOfEntry;

		return $this->entreis=$this->transformResults($entreis);
    }

	
	/**
	 * Return the array of aliased fields if exists, otherwise the original array of fields.
	 * 
	 * @return array
	 */
	protected function getAliassedFields()
	{
	    if(!empty($this->fieldsAliases))
		    return $this->fieldsAliases;
	
	    return $this->fields;
	}
	
	
	/**
	 * Return the alias for the table name if exists, otherwise the original table name.
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
	 * Replace the fields names with the fields aliases names.
	 *
	 * @param  array $entries
	 * @return array
	 */
	protected function transformResults($entries)
	{
		if(empty($this->fieldsAliases))
		    return $entries;

        $transformedArray = array();
		
		$k=0;
		foreach($entries as $entry)
		{
			$fieldsAliases = $this->fieldsAliases;

			$i = 0;
			foreach($entry as $origKey => $value)
			{
				$newKey = $fieldsAliases[$i];

				$transformedArray[$k][$newKey] = $value;
				
				$i++;
			}
			$k++;
		}

		return $transformedArray;
	}
	
	
	/**
	 * Print the results.
	 *
	 * @param  string $str
	 * @return string
	 */
	protected function printResult($str)
	{
	    echo $str;
	}
}
