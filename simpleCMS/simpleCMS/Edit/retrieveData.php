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
$test = $_SESSION["validated"];
// Check if the requester is authorized
if ($_SESSION["validated"] != true)
{
	die(json_encode(array("error" => "unauthorized")));
}

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

if ($data["data"] === "pages")
{
    // instantiate the DB helper class
    $inf = new DBInfo();
    $db = new DBHelper($inf->host(),$inf->username(),$inf->pass(),$inf->dbName(),$inf->port());


    // Ask the DB for all pages in the database
    $rows = $db->selectPages();

    // Close the db
    $db->close();
    // If we got rows back
    if ($rows !== false)
    {
        // Send the rows back
        echo json_encode($rows);
    }
    else
    {
        die(json_encode(array("error"=>"no pages")));
    }
}
else if ($data["data"] === "items")
{
    $pageId = $data["pageId"];

    // instantiate the DB helper class
    $inf = new DBInfo();
    $db = new DBHelper($inf->host(),$inf->username(),$inf->pass(),$inf->dbName(),$inf->port());

    $rows = array();
    $returnedRows = array();

    // Ask the DB for all headings related to this page
    $rows = $db->selectItems("Heading", $pageId);

    // If we got headings back
    if ($rows !== false)
    {
        for ($i = 0; $i < count($rows); $i++)
        {
            $rows[$i]["itemType"] = "Heading";
        }
        array_push($returnedRows, $rows);
    }

    // Ask the DB for all sections related to this page
    $rows = $db->selectItems("Section", $pageId);

    if ($rows !== false)
    {
        for ($i = 0; $i < count($rows); $i++)
        {
            $rows[$i]["itemType"] = "Section";
        }
        array_push($returnedRows, $rows);
    }

    // Ask the DB for all Images related to this page
    $rows = $db->selectItems("Image", $pageId);

    if ($rows !== false)
    {
        for ($i = 0; $i < count($rows); $i++)
        {
            $rows[$i]["itemType"] = "Image";
        }
        array_push($returnedRows, $rows);
    }


    // Close the db
    $db->close();


    if (count($returnedRows) > 0)
    {
        echo json_encode($returnedRows);
    }
    else
    {
        echo json_encode(array("error"=>"no items"));
    }
}