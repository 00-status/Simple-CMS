<?php

namespace simpleCMS\DB
{
    use mysqli;

	/**
	 * Uses mysqli to interact with a MySQL database
	 *
	 * DBHelper description.
	 *
	 * @version 1.0
	 * @author Liam
	 */
	class DBHelper extends mysqli
	{
        function __construct($host, $username, $password, $dbname, $port)
        {
            // Have the parent class do its thing
            parent::__construct($host, $username, $password, $dbname, $port);

            // Check the connection

            // Create all of the tables in the DB if they do not exist
            $this->createTables();
        }

        // Create Tables
        public function createTables()
        {
            $query = "";

            // Create the Page Table
            $query = "CREATE TABLE IF NOT EXISTS Page (
                pageId INT PRIMARY KEY,
                title VARCHAR(30) NOT NULL
            )";
            $this->query($query);


            // Create the Section Table
            $query = "CREATE TABLE IF NOT EXISTS Section (
                sectionId INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
                pageId INT NOT NULL,
                itemIndex INT NOT NULL,
                content VARCHAR(5000) NOT NULL
            )"; 
            $this->query($query);

            // Create the Image Table
            $query = "CREATE TABLE IF NOT EXISTS Image (
                imageId INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
                pageId INT NOT NULL,
                itemIndex INT NOT NULL,
                path VARCHAR(40) NOT NULL,
                alt VARCHAR(30) NOT NULL
            )";
            $this->query($query);

            // Create the Heading Table
            $query = "CREATE TABLE IF NOT EXISTS Heading (
                headingId INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
                pageId INT NOT NULL,
                itemIndex INT NOT NULL,
                content VARCHAR(20) NOT NULL,
                headingType INT NOT NULL
            )";
            $this->query($query);
        }



        // SELECT FUNCTIONS
        /**
         * Selects all pages that are stored from the DB
         * @return \array|boolean
         */
        public function selectPages()
        {
            // Assume that there are no pages
            $returns = false;
            // An empty array for any rows that are returned later
            $rows = array();

            // The query to select all pages
            $query = "SELECT * FROM `Page` WHERE 1";
            // Execute the query
            $result = $this->query($query);

            // Check if any results were returned
            if ($result !== false && $result->num_rows > 0)
            {
                // Loop through the results
            	while ($row = $result->fetch_assoc())
                {
                    // Push the returned row into the array of rows
                    array_push($rows, $row);
                }
                // Set returns to be the rows
                $returns = $rows;
            }

            // Return the results
            return $returns;
        }
        public function selectPage($pageId)
        {
            // Assume the select failed
            $returns = false;

            // Prepare a SQL statement
            $stmt = $this->prepare("SELECT * FROM Page WHERE pageId=?");
            $stmt->bind_param("i", $pageId);

            // Execute the SQL statement
            $stmt->execute();

            // Get the results
            $result = $stmt->get_result();

            // Set the results to our return variable
            if ($result->num_rows > 1)
            {
                $returns = $result->fetch_assoc();
            }

            // Return either false or the row found
            return $returns;
        }
        public function selectItems($table, $pageId)
        {
            // Assume that there are no entries that correspond to the given page
            $returns = false;
            // An empty array for any entries that are returned later
            $rows = array();

            // Grab the base Items for the page from PageItem
            $query = "SELECT * FROM $table WHERE pageId='$pageId'";
            $result = $this->query($query);

            // If we get some entries returned
            if ($result !== false && $result->num_rows > 0)
            {
                // Loop through the results
            	while ($row = $result->fetch_assoc())
                {
                    // Push the entries onto the array
                    array_push($rows, $row);
                }
                // Set returns to be the retrieved rows
                $returns = $rows;
            }

            // Return any entries that we get
            return $returns;
        }




        // INSERT AND UPDATE
        function updateSection($pageId, $itemIndex, $content, $sectionId)
        {
            $result = false;

            $stmt = $this->prepare(
                "UPDATE `Section` SET `pageId`=?,`itemIndex`=?,`content`=?
                    WHERE `sectionId`=?");

            $stmt->bind_param("iisi", $pageId, $itemIndex, $content, $sectionId);


            $result = $stmt->execute();

            return $result;
        }
        function updateHeading($pageId, $itemIndex, $content, $headingType, $headingId)
        {
            $result = false;

            $stmt = $this->prepare(
                "UPDATE `Heading` SET `pageId`=?,`itemIndex`=?,`content`=?,`headingType`=?
                    WHERE `headingId`=?");

            $stmt->bind_param("iisii", $pageId, $itemIndex, $content, $headingType, $headingId);


            $result = $stmt->execute();

            return $result;
        }

        function insertSection($pageId, $itemIndex, $content)
        {
            $result = false;

            $stmt = $this->prepare("INSERT INTO Section (pageId, itemIndex, content) VALUES (?,?,?)");
            $stmt->bind_param("iis", $pageId, $itemIndex, $content);

            $result = $stmt->execute();

            return $result;
        }
        function insertHeading($pageId, $itemIndex, $content, $headingType)
        {
            $result = false;

            $stmt = $this->prepare("INSERT INTO Heading (pageId, itemIndex, content, headingType) VALUES (?,?,?,?)");
            $stmt->bind_param("iisi", $pageId, $itemIndex, $content, $headingType);

            $result = $stmt->execute();

            return $result;
        }



        // DELETE
        function deleteHeading($itemId)
        {
            $result = false;

            $stmt = $this->prepare("DELETE FROM Heading WHERE headingId = ?");
            $stmt->bind_param("i", $itemId);

            $result = $stmt->execute();

            return $result;
        }
        function deleteSection($itemId)
        {
            $result = false;

            $stmt = $this->prepare("DELETE FROM Section WHERE sectionId = ?");
            $stmt->bind_param("i", $itemId);

            $result = $stmt->execute();

            return $result;
        }




        // TEST FUNCTIONS
        public function insertData()
        {
            $stmt = $this->prepare("INSERT INTO Page (pageId, title) VALUES (?, ?);");
            $stmt->bind_param("ss", $pageId, $title);

            for ($i = 1; $i < 4; $i++)
            {
                $pageId = $i;
                $title = "Page: " . $i;

            	$stmt->execute();
            }

            // Insert some sections for us to play with
            $stmt = $this->prepare("INSERT INTO Section (pageId, itemIndex, content) VALUES (?, ?, ?)");
            $stmt->bind_param("sss",$pageId, $itemIndex, $contents);

            $pageId = 1;
            for ($i = 1; $i < 4; $i++)
            {
                if ($i == 3)
                {
                    $pageId = 2;
                    $itemIndex = 0;
                    $contents = "Contents for section:\n " . $i;
                    $stmt->execute();
                }
                else
                {
                    $itemIndex = $i;
                    $contents = "Contents for section:\n " . $i;
                    $stmt->execute();
                }
            }

            // Insert a heading for the first page
            $stmt = $this->prepare("INSERT INTO Heading (pageId, itemIndex, content, headingType) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss",$pageId, $itemIndex, $contents, $headingType);

            $pageId = 1;
            $itemIndex = 0;
            $contents = "Heading for Page 1";
            $headingType = 1;
            $stmt->execute();

        }
        public function dropTables()
        {
            $query = "DROP TABLES Page, Image, Heading, Section";

            $this->query($query);
        }
	}
}