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
   	    $this->objToArray();
	   
	    $arr = $this->entreis;	
			
   	    return json_encode($arr);
   }
}
