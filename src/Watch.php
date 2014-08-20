<?php
namespace Reshetech\MyApi;

class Watch extends Db
{
	/**
	 * The table name to count the number of watches (default myapi_watches).
	 *
	 * @var string
	 */	
	protected $tableName = 'myapi_watches';
	
	/**
	 * The fields to query.
	 *
	 * @var array
	 */	
	protected $fields    = array('myapi_auth_id');
	
	/**
	 * We expect only 1 result.
	 *
	 * @var array
	 */	     
	protected $limit     = array(0,1);

	
	/**
	 * Exposed method to update the number of watches.
	 *
	 * @param  int $userId
	 * @return mixed
	 */	
    public function updateWatches($userId)
	{
		$userId  = (int)$userId;
		
		$now = date('Y-m-d H:i:s');
			
		$insert = array(
		    0=>array('myapi_auth_id','=',$userId),
			1=>array('num_watches','=',1),
			2=>array('last_visit','=',$now),
		);

        $this->setInsert($insert);

        $onDuplicateKey = array(
            0=>array('num_watches','+1'),
			1=>array('last_visit',$now)
        );		
		
        $this->setOnDuplicateKey('update',$onDuplicateKey);

		$results=$this->getResults();

		if(!$results)		
			error_log("Error: failed update number of watches.\n");
	}
}
	