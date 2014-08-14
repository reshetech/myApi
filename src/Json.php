<?php
namespace Reshetech\MyApi;

class Json extends OutputAbstract
{
   	
   /**
	 * Output json encoded data.
	 *
	 * @return string
	 */  
   public function get()
   {
   	    if(!empty($this->errors))
		    return $this->printResult(Utilis::arrayToString($this->errors));
		
		$this->objToArray();
	   
	    $arr = $this->entreis;

        Utilis::writeHeader('Content-Type: application/json',200,false);
	   
        $this->printResult(json_encode($arr));
   }
}
