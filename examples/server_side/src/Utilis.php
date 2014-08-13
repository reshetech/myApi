<?php
namespace Reshetech\MyApi;

class Utilis
{
	/**
	 * Make a string out of array.
	 *
	 * @param  array
	 * @return string
	 */ 	   
	public static function arrayToString($arr)
	{
	    if(!is_array($arr) || empty($arr)) return;
	
		$string = '';

        foreach($arr as $item)
        {
            $string .= $item;
        }		

        return $string;
   }
}
