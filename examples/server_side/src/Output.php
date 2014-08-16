<?php
namespace Reshetech\MyApi;

class Output
{
    /**
	 * The object that holds the output.
	 *
	 * @var Reshetech\MyApi\Xml
	 * @var Reshetech\MyApi\Json
	 */
	protected $obj;
	
	/**
	 * The format for output: json, xml or false.
	 *
	 * @var mixed
	 */		    
    protected $format = false;
	
	/**
	 * The acceptable formats.
	 *
	 * @var array
	 */	
	protected $acceptableFormats = array('xml','json');

	
	/**
	 * Creates the object to hold the output.
	 *
	 * @param  string $format
	 * @param  array  $results
	 * @return the object
	 */
	public function create($format,$results=false)
	{
		if(!$results) return false;
		
		$format = $this->setFormat($format);
		
		$format = ucfirst(trim(strtolower($format)));

		$class  = 'Reshetech\MyApi\\'.$format;
    
		return $this->obj = new $class($results);	
	}
	
	/**
	 * Set an alias to the table name to be used as a root element for xml.
	 *
	 * @param string $str
	 * @return mixed
	 */
	public function setTableAlias($str)
	{
	    if(isset($str) && $str !== '' && is_string($str))
		{
			$this->obj->setAliasedTableName(trim($str));
		}
	}
	
	/**
	 * Set the alias for the fields names.
	 *
	 * @param array @arr
	 * @return
	 */
	public function setFieldsAlias($arr)
	{
	    if(isset($arr) && !empty($arr))
		    $this->obj->setAliasedFieldsNames($arr);
	}
	
	/**
	 * Output the results
	 *
	 * @return string
	 */
	public function get()
	{
		return $this->obj->get();
	}
	
	
	/**
	 * Get the output format.
	 *
	 * @param  string $str
	 * @return string
	 */
	public function setFormat($str)
	{
		if(!in_array($str,$this->acceptableFormats)) 
		    return $this->format='json';

		return $this->format = $str;
	}
}
