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

// Save or update each item from the given array
foreach ($data as $item)
{
    switch ($item["itemType"])
    {
        case "Section":
            if (isset($item["sectionId"]))
            {
                $success = $db->updateSection($item["pageId"],$item["itemIndex"],$item["content"], $item["sectionId"]);
            }
            else
            {
                $success = $db->insertSection($item["pageId"],$item["itemIndex"],$item["content"]);
            }
            break;
        case "Heading":
            if (isset($item["headingId"]))
            {
                $success = $db->updateHeading($item["pageId"],$item["itemIndex"],$item["content"], $item["headingType"], $item["headingId"]);
            }
            else
            {
                $success = $db->insertHeading($item["pageId"],$item["itemIndex"],$item["content"], $item["headingType"]);
            }
            break;
    	default:
    }
}

// Close the db
$db->close();

echo json_encode(array("success"=>$success));