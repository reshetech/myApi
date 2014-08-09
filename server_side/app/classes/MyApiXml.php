<?php
namespace app\classes;

class MyApiXml extends MyApiOutput
{
	/**
	 * Output xml data.
	 *
	 * @return string
	 */ 	   
	public function output()
	{
	    $this->objToArray();
	   
	    $arr = $this->entreis;
	   
        $output  = '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL;
       
        $output .= "<group-".$this->tableName.">".PHP_EOL;
	   
        $numOfRows = $this->numOfEntries;
        for($i = 0 ; $i < $numOfRows; $i++)
        {
             $fields = $this->fields;	
				
             $output .= "<".$this->tableName.">".PHP_EOL;
             
             foreach($fields as $field)
			 {
			 	$output .= "<".$field.">".$arr[$i][$field]."</".$field.">".PHP_EOL;
			 }
			 
             $output .= "</".$this->tableName.">".PHP_EOL;
	    }
        $output .= "</group-".$this->tableName.">";
	   
        return $output;
   }
}
