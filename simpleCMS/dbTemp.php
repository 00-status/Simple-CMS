<?php

use simpleCMS\DB\DBHelper;
use simpleCMS\DB\DBInfo;

spl_autoload_register(function($class)
{
    require_once $class . '.php';
});

$inf = new DBInfo();
$db = new DBHelper($inf->host(),$inf->username(),$inf->pass(),$inf->dbName(),$inf->port());

$db->selectItems("Section\\",1);

$db->close();
$db = null;


echo "Result: " + $result;
?>