<?php
require 'autoload.php';

/**
 * Define your database credentials here.
 */
$config = array(
    'database' => array(
        'driver' => 'mysql',
        'host'   => '127.0.0.1',
        'dbName' => '',
        'user'   => '',
        'pass'   => ''
     )
);


/**
 * Define the constants to connect with the database according to the $config array.
 */
define('DB_DRIVER', $config['database']['driver']);
define('DB_HOST', $config['database']['host']);
define('DB_USER', $config['database']['user']);
define('DB_PASS', $config['database']['pass']);
define('DB_NAME', $config['database']['dbName']);
