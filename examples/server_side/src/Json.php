<?php
namespace Reshetech\MyApi;

class Json extends Output
{
   	
   /**
	 * Output json encoded data.
	 *
	 * @return string
	 */  
   public function output()
   {
   	    if(!empty($this->errors))
		    return Utilis::arrayToString($this->errors);
		
		$this->objToArray();
	   
	    $arr = $this->entreis;	
			
   	    return json_encode($arr);
   }
}
