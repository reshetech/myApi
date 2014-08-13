<?php
namespace Reshetech\MyApi;

class Auth extends Db
{
	/**
	 * The user authentication is by default against an auhth table (default myapi_auth).
	 *
	 * @var string
	 */	
	protected $tableName = 'myapi_auth';
	
	/**
	 * The fields to query in the select statement.
	 *
	 * @var array
	 */	
	protected $fields    = array('id');
	
	/**
	 * The authentication should by limited to uniquer user.
	 *
	 * @var array
	 */	     
	protected $limit     = array(0,1);
	
							
	/**
	 * Exposed method that checks if the user exists.
	 *
	 * @param  string $key
	 * @param  string $pass
	 * @return boolean
	 */	
    public function isAuth($key,$pass)
	{
		$key  = sha1(preg_replace('/[^0-9a-zA-Z-_]/','',$key));
		$pass = sha1(preg_replace('/[^0-9a-zA-Z-_]/','',$pass));
		
		// The distant client should meet these 3 conditions to be authenticated.			
		$where     = array(
                         0=>array('key','=',$key,'and'),
                         1=>array('pass','=',$pass,'and'),
                         2=>array('active','=','1')
					);	
			
		$this->setWhere($where);

		$results=$this->getResults();

		if(!$results) return false;
		
		$id=$results[0]->id;

		if(isset($id) && (int)$id > 0) return true;
		
		return false;
	}
}
	