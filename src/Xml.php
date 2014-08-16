<?php
namespace Reshetech\MyApi;

class Xml extends OutputAbstract
{
	/**
	 * Output xml data.
	 *
	 * @return header
	 * @return string
	 */ 	   
	public function get()
	{  
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
	   
        $this->views->setXmlHeader()->getHeader();
		
		$this->views->writeToScreen($output,true);
   }
}
