<?php
namespace app\classes;

class MyApiOutputFactory
{
    public static function createOutput($str,$results,$tableNameAlias,$fieldsAliases)
	{
		$format = ucfirst(trim(strtolower($str)));

		$class    = '\app\classes\\'.'MyApi'.$format;
		
		$obj = new $class($results,$tableNameAlias,$fieldsAliases);	

		return $obj->output();
	}
}
