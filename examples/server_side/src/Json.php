<?php
namespace Reshetech\MyApi;

class Json extends OutputAbstract
{
   	
    /**
	 * Output json encoded data.
	 *
	 * @return mixed
	 */  
    public function get()
    {
		$this->objToArray();
		
	    $arr = $this->entries;

        if(!empty($this->errors))
        {
            $this->views->ok()->getHeader();
			
			$errors = Utilis::arrayToString($this->errors);
			
			return $this->views->writeToScreen($errors,true);
        }		
		
		$this->views->setJsonHeader()->getHeader();
		
		$output = json_encode(
		    array_map(
				function($key, $value) 
				{ 
				    return array($key, $value); 
				},
				array_keys($arr),
				array_values($arr)
		    ),
			JSON_PRETTY_PRINT
		);
		
		$this->views->writeToScreen($output,true);
    }
}
