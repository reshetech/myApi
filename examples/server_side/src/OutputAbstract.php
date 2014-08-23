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
	protected $fields = array();
		
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
	protected $entries=array();
	
	/**
	 * The number of records to be presented (without pagination).
	 *
	 * @var int
	 */
	protected $recordNum;
	
	/**
	 * The errors array.
	 *
	 * @var array
	 */ 
    protected $errors   = array();
	
	/**
	 * Hold the views object.
	 *
	 * @var Views
	 */ 
	protected $views;
	
    
	/**
	 * Distribute the data of the database query between the class' variables.
	 *
	 * @param  array $results
	 * @return mixed
	 */
	public function __construct(array $results)
    {
	    $this->tableName = $results[0];
			   
	    $this->fields    = $results[1];
		
		$this->results   = $results[2];
		
	    $this->views=new Views();
    }
	
	
	/**
	 * Get the table name variable.
	 *
	 * @return string
	 */
	public function getTableName()
	{
	    return $this->tableName;
	}
	
	/**
	 * Get the fields names.
	 *
	 * @return array
	 */
	public function getFields()
	{
	    return $this->fields;
	}
	

	/**
	 * Turn the object returned from the database query into an array.
	 *
	 * @return array
	 */
    protected function objToArray()
    {
		$entries=array();
				
        $numOfEntry = 0;
		
		$recordNum  = 0;
		
		$results    = $this->results;

		
		$countResults = count($results);
		
		$fields=$this->fields;
		
		$countFields  = count($fields);
		
		foreach($results as $result)
        {
            foreach($fields as $field)
		    {
				if(isset($result->$field))
				{
				    $entries[$numOfEntry][$field] = $result->$field;
					$recordNum++;
				}
		    }
			
			$numOfEntry++;
		}
		
		$this->recordNum   =$recordNum/$countFields;

		
		if(isset($results['pagination']))
		{
			$paginationArray = $results['pagination'];
			
			foreach($paginationArray as $field => $value) 
			{
			   $entries['pagination'][$field] = $value;
			}			
		}

		return $this->entries=$entries;
    }
	
	
	/**
	 * Output encoded data.
	 *
	 * @return string
	 */
	public abstract function get();
}
