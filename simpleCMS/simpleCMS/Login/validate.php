<?php

session_start();

use simpleCMS\DB\DBHelper;
use simpleCMS\DB\DBInfo;

spl_autoload_register(function($class)
{
    require_once '..\\..\\' . $class . '.php';
});

// Check the POST variable
if ($_SERVER['REQUEST_METHOD'] != 'POST')
{
	die("Invalid Credentials");
}

$username = trim($_POST['user']);
$password = trim($_POST['pass']);


if (empty($username) || empty($password))
{
	die("Invalid Credentials");
}


// Validate the username
if (strlen($username) < 3 || strlen($username) > 15)
{
    die("Invalid Credentials");
}
// Validate the password
if (strlen($password) < 8 || strlen($password) > 30)
{
    die("Invalid Credentials");
}


// Check if the user exists in the DB
$inf = new DBInfo();
$db = new DBHelper($inf->host(),$inf->username(),$inf->pass(),$inf->dbName(),$inf->port());

// Attempt to get the user from the DB
$user = $db->selectUser($username);

if ($user === false)
{
	die("Invalid Credentials");
}

if ($user['name'] === $username && password_verify($password, $user["password"]))
{
    // Set Session Cookie
    $_SESSION{"validated"} = true;
    // Redirect to the edit page
    header("Location: ..\\Edit\\edit.php");
    die();
}


// Close the DB
$db->close();

// Verify username and password credentials

