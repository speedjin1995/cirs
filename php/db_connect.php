<?php
date_default_timezone_set('Asia/Kuala_Lumpur');
// Load DB credentials securely
$config = include(dirname(__DIR__, 3) . '/db_config.php');

// Establish database connection
$db = mysqli_connect($config['host'], $config['username'], $config['password'], $config['database']);

if(mysqli_connect_errno()){
    echo 'Database connection failed with following errors: ' . mysqli_connect_error();
    die();
}
?>