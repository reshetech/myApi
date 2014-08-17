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
