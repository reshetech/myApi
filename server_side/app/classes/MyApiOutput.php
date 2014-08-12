<?php
namespace app\classes;

abstract class MyApiOutput
{
	/**
	 * The name of the returned results according to the queried table in the database.
	 *
	 * @var string
	 */	
	protected $tableName; 
	
	/**
	 * Array of field names for the output according to the fields in the queried database table.
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
	 * Replaces the table name as the root name for the results in XML.
	 *
	 * @var array
	 */
    protected $tableNameAlias='';	
		
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
	 * Distributes the data of the database query between the class' variables.
	 *
	 * @param  array
	 * @return mixed
	 */
	public function __construct(array $results, $tableNameAlias, array $fieldsAliases)
    {
	    $this->tableName = $results[0];
			   
	    $this->fields    = $results[1];
		
		$this->results   = $results[2];
		
		if(isset($tableNameAlias) && $tableNameAlias !== '')
		{
		    $tableNameAlias = trim($tableNameAlias);
			
			if(is_string($tableNameAlias))
			    $this->tableNameAlias = $tableNameAlias;
			else	
			    $this->errors[]='The table alias is not a string or an empty string.';
		}

		if(isset($fieldsAliases) && !empty($fieldsAliases))
		{
		    if($this->isValidFieldsAliases($fieldsAliases))
				return $this->fieldsAliases = $fieldsAliases;
			else
			    $this->errors[]='The number of items in your fields aliases array does not match the number of items in your fields array.';
		}
    }
    
	
	/**
	 * Turns the object that was returned from the database query into an array.
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
	 * Checks if the fields aliases array is valid.
	 * 
	 * @return boolean
	 */
	protected function isValidFieldsAliases($arr)
	{
		if(!is_array($arr)) return false;
		
		if(count($arr)<>count($this->fields)) return false;
		
		return true;
	}
	
	/**
	 * Replaces the fields names with the fields aliases names.
	 *
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
}
