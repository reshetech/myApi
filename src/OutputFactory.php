<?php
namespace Reshetech\MyApi;

class OutputFactory
{
    public static function createOutput($str,$results,$tableNameAlias,$fieldsAliases)
	{
		$format = ucfirst(trim(strtolower($str)));

		$class    = 'Reshetech\MyApi\\'.$format;
		
		$obj = new $class($results,$tableNameAlias,$fieldsAliases);	

		return $obj->output();
	}
}
