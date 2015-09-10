<?php
/**
 * Database configuration
 */
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'yU9UdG7wQZRVDfBU');
//define('DB_PDO', 'mysql:unix_socket=/cloudsql/liftmo-1056:test;dbname=prod')
define('DB_HOST', null);
define('DB_NAME', 'lumivote');
define('DB_UNIX', '/cloudsql/liftmo-1056:v1');

define('USER_CREATED_SUCCESSFULLY', 0);
define('USER_CREATE_FAILED', 1);
define('USER_ALREADY_EXISTED', 2);

define('USERPROFILE_CREATED_SUCCESSFULLY', 0);
define('USERPROFILE_CREATE_FAILED', 1);
define('USERPROFILE_ALREADY_EXISTED', 2);
?>
