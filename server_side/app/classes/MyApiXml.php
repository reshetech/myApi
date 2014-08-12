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
	    if(!empty($this->errors))
		    return MyApiUtilis::arrayToString($this->errors);
		    
		$this->objToArray();
	   
		$arr = $this->entreis;
		
		$tableName = $this->getAliasedTableName();

		$output  = '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL;

		$output .= "<group-".$tableName.">".PHP_EOL;
	   
		$numOfRows = $this->numOfEntries;
		for($i = 0 ; $i < $numOfRows; $i++)
		{
			$fields = $this->getAliassedFields();
	
			$output .= "<".$tableName.">".PHP_EOL;
			 
			foreach($fields as $field)
			{
				$output .= "<".$field.">".$arr[$i][$field]."</".$field.">".PHP_EOL;
			}
			 
			$output .= "</".$tableName.">".PHP_EOL;
		}
		$output .= "</group-".$tableName.">";
	   
        return $output;
   }
}
