<?php

// Specify the header
header('Content-type: application/json');

use simpleCMS\DB\DBHelper;
use simpleCMS\DB\DBInfo;

spl_autoload_register(function($class)
{
    require_once "..\\..\\" . $class .'.php';
});


$contentType = null;

// Check is the content type is set
if (isset($_SERVER["CONTENT_TYPE"]))
{
	$contentType = $_SERVER["CONTENT_TYPE"];
}

// Check if the content type is correct
if ($contentType !== "application/json")
{
    die(json_encode(array('error' => 'No data sent')));
}


// Get the data from php://input
$data = trim(file_get_contents("php://input"));
$data = json_decode($data, true);


if ( !(isset($data)) )
{
    die(json_encode(array('error' => 'No data sent')));
}

$success = false;

// Instantiate a new db connection
$inf = new DBInfo();
$db = new DBHelper($inf->host(),$inf->username(),$inf->pass(),$inf->dbName(),$inf->port());


switch ($data["itemType"])
{
    case "Heading":
        $success = $db->deleteHeading($data["headingId"]);

        break;
    case "Section":
        $success = $db->deleteHeading($data["sectionId"]);
        break;
	default:
}

echo json_encode(array("success"=>$success));

