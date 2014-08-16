<?php
require 'init.php';

/**
 * Define the constants to connect with the database from the $config array.
 */

define('DB_DRIVER', $config['database']['driver']);
define('DB_HOST', $config['database']['host']);
define('DB_USER', $config['database']['user']);
define('DB_PASS', $config['database']['pass']);
define('DB_NAME', $config['database']['dbName']);
