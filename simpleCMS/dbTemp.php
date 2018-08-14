<?php

use simpleCMS\DB\DBHelper;
use simpleCMS\DB\DBInfo;

spl_autoload_register(function($class)
{
    require_once $class . '.php';
});

$inf = new DBInfo();
$db = new DBHelper($inf->host(),$inf->username(),$inf->pass(),$inf->dbName(),$inf->port());

$result = $db->query("DROP TABLE Page");
$result = $db->query("DROP TABLE Image");
$result = $db->query("DROP TABLE Section");
$result = $db->query("DROP TABLE Heading");

$db->close();
$db = null;

echo "Result: " . $result;
?>