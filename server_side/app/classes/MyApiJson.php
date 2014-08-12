<?php
namespace app\classes;

class MyApiJson extends MyApiOutput
{
   	
   /**
	 * Output json encoded data.
	 *
	 * @return string
	 */  
   public function output()
   {
   	    if(!empty($this->errors))
		    return MyApiUtilis::arrayToString($this->errors);
		
		$this->objToArray();
	   
	    $arr = $this->entreis;	
			
   	    return json_encode($arr);
   }
}
