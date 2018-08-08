<?php
session_start();

// Check if the user is validated
if ($_SESSION["validated"] != true)
{
	echo json_encode(array("error"=>"not validated"));
    die();
}

// Set variables for validating the image
$uploaded = false;
$file = $_FILES["file"];
// path that will be sent back if the upload succeeds
$path = "\\simpleCMS\\Images\\" . basename($file["name"]);
// The relative path to where the image will be stored
$targetFile = "../Images/" . basename($file["name"]);

// Check if an image was sent
if (!isset($_FILES["file"]))
{
	echo json_encode(array("error"=>"Please select an image!"));
    die();
}

// Check if the image size is appropriate
// Only allow images that are under 2MB
if ($file["size"] <= 2097152 && $file["size"] != 0)
{
    // Check if the image type is actually correct
    // We only accept jpegs and pngs
    if ($file["type"] == "image/jpg" || $file["type"] == "image/jpeg" ||
        $file["type"] == "image/png" || $file["type"] == "image/gif" )
    {
        // Check if this image already exists
        if (!file_exists($targetFile))
        {
            // Attempt to move the file from the temp location to the permanent location
            if ( move_uploaded_file($file["tmp_name"], $targetFile) )
            {
                echo json_encode(array("successPath"=>$path,"successName"=>basename($file["name"]) ));
            }
            else
            {
                echo json_encode(array("error"=>"Unable to upload image."));
            }
        }
        else
        {
            echo json_encode(array("error"=>"This image already exists."));
        }
    }
    else
    {
        echo json_encode(array("error"=>"The image must be either a png or a jpeg."));
    }
}
else
{
    echo json_encode(array("error"=>"The image must be under 2MB in size."));
}