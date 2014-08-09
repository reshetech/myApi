<?php
namespace app\classes;

class MyApiFormat
{
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
	 * Define the output format or false.
	 *
	 * @return mixed
	 */
	public function __construct($format)
	{
		$format = trim(strtolower(preg_replace('/[^0-9a-zA-Z-_]/','',$format)));

		if(!in_array($format,$this->acceptableFormats)) 
		    return $this->format='json';

		return $this->format = $format;
	}

	
	/**
	 * Get the output format.
	 *
	 * @return mixed
	 */
	public function getFormat()
	{
		return $this->format;
	}
}
