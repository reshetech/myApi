<?php
use \Reshetech\MyApi;

require 'src/config/init.php';

// The name of the table to pull results from. e.g. 'hotels'
$tableName = 'hotels';

// You can give a different name to the table so the results will have a name which is different than the name of the table.
//$tableNameAlias = 'resort';

// Name of fields in the table to pull results from. e.g. array('hotel_name','country','date_start','date_end','price')
$fields    = array('hotel_name','country','date_start','date_end','price');

// You can also give a different name to each field for a more friendly and secure field names. e.g. array('name','region','start','end','price')
//$fieldsAliases = array('name','region','start','end','price');

// Order by clause example. e.g. array('price','asc')
$orderBy = array('price','desc');
	

if(isset($_POST['key']) && isset($_POST['pass']) && isset($_POST['where']))
{
	// Check if the user is authenticated according to his user-key and password.	
	$auth = new MyApi\Auth();
	
	$auth->isAuth($_POST['key'],$_POST['pass']);	
			
			
			
	// Query the database for the selected data.
	$posts  = new MyApi\Posts();
	
	$posts->create($tableName,$fields,$_POST['where']);

	// Optional 1: maximum number of records to return.
	if(isset($_POST['num']))
		$posts->setNum($_POST['num']);
		
	// Optional 2: how to order the records.
	if(isset($orderBy))
	    $posts->setOrderBy($orderBy);
	
	// The results returned from the query.
	$results=$posts->get();

	
	
	// Prepare the output.
	$output = new myApi\Output();
	
	$output->create($_POST['format'],$results);
	
	// Optional 1: set an alias to the table name.
	if(isset($tableNameAlias))
	    $output->setTableAlias($tableNameAlias);
	
	// Optional 2: set an alias to the table fields.
	if(isset($fieldsAliases))
	    $output->setFieldsAlias($fieldsAliases);	

	// Output the results as xml or json formats.
	$output->get();
}
else
{
    echo "<p>Please insert a query string.</p>";
	echo "<p><a href='http://reshetech.co.il/myApi/#query'>Read the documentation</a></p>";
}

