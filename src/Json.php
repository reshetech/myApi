<?php
namespace Reshetech\MyApi;

class Json extends OutputAbstract
{
   	
    /**
	 * Output json encoded data.
	 *
	 * @return header
	 * @return string
	 */  
    public function get()
    {
		$this->objToArray();
		
	    $arr = $this->entreis;

        if(!empty($this->errors))
        {
            $this->views->ok()->getHeader();
			
			$errors = Utilis::arrayToString($this->errors);
			
			return $this->views->writeToScreen($errors,true);
        }		
		
		$this->views->setJsonHeader()->getHeader();
		
        $output = json_encode($arr);
		
		$this->views->writeToScreen($output,true);
    }
}
