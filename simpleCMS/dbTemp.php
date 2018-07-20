<?php

use simpleCMS\DB\DBHelper;
use simpleCMS\DB\DBInfo;

spl_autoload_register(function($class)
{
    require_once $class . '.php';
});

$inf = new DBInfo();
$db = new DBHelper($inf->host(),$inf->username(),$inf->pass(),$inf->dbName(),$inf->port());

$pass = password_hash("simplePass", PASSWORD_DEFAULT);
$stmt = $db->prepare("INSERT INTO User (name, password) VALUES ('admin', ?)");
$stmt->bind_param("s", $pass);

$stmt->execute();
echo "Result: " + $result;
?>