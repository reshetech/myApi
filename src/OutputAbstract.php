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
	 * The views object.
	 *
	 * @var object Views
	 */ 
	protected $views;
	
    
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
		
	    $this->views=new Views();
    }
	
	
	/**
	 * Get the table name variable.
	 */
	public function getTableName()
	{
	    return $this->tableName;
	}
	
	/**
	 * Get the fields names.
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

		return $this->entreis=$entreis;
    }
	
	
	/**
	 * Output encoded data.
	 *
	 * @return string
	 */
	public abstract function get();
}
