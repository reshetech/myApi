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
	 * Distributes the data of the database query between the class' variables.
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
	 * Turns the object that was returned from the database query into an array.
	 *
	 * @return array
	 */
    protected function objToArray()
    {
        $entreis=array();
				
        $numOfEntries = 0;
		$results=$this->results;
	    foreach($results as $result)
        {
           $fields=$this->fields;
           foreach($fields as $field)
		   {
               $entreis[$numOfEntries][$field] = $result->$field;
		   }
           
			$numOfEntries++;
         }
		 $this->numOfEntries=$numOfEntries;
		
		 return $this->entreis=$entreis;
    }
}
