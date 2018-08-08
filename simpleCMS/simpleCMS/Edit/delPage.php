<?php
session_start();

// Specify the header
header('Content-type: application/json');

use simpleCMS\DB\DBHelper;
use simpleCMS\DB\DBInfo;

spl_autoload_register(function($class)
{
    require_once "..\\..\\" . $class .'.php';
});

// Check if the requester is authorized
if ($_SESSION["validated"] != true)
{
	die(array("error" => "unauthorized"));
}



$contentType = null;

// Check if the content type is set
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
        $success = $db->deleteSection($data["sectionId"]);
        break;
    case "Image":
        // Retrieve the image path directly from the DB
        $dbImage = $db->selectImage($data["imageId"]);
        // Delete the image from the server
        unlink("../Images/" . $dbImage["name"]);

        // Delete the image entry from the database
        $success = $db->deleteImage($data["imageId"]);
        break;
	default:
}

$db->close();
$db = null;

echo json_encode(array("success"=>$success));

