<?php

namespace simpleCMS\DB
{
    use mysqli;

	/**
	 * Uses mysqli to interact with a MySQL database
	 *
	 */
	class DBHelper extends mysqli
	{
        function __construct($host, $username, $password, $dbname, $port)
        {
            // Have the parent class do its thing
            parent::__construct($host, $username, $password, $dbname, $port);

            // Check the connection
            if ($this->connect_error) {
                die("Connection failed: " . $this->onnect_error);
            }

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

            // Create the User Table
            $query = "CREATE TABLE IF NOT EXISTS User (
                name VARCHAR(15) NOT NULL UNIQUE,
                password VARCHAR(300) NOT NULL
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
        /**
         * Attempts to select a page from the DB based on the given pageId
         * If a page is not found, then returns false.
         * @param mixed $pageId
         * @return \array|boolean
         */
        public function selectPage($pageId)
        {
            // Prepare a SQL statement
            $stmt = $this->prepare("SELECT * FROM Page WHERE pageId=?");
            $returns = $stmt->bind_param("i", $pageId);

            // Execute the SQL statement
            $returns = $stmt->execute();

            if ($returns != false)
            {
                // Get the results
                $result = $stmt->get_result();

                // Set the results to our return variable
                if ($result->num_rows > 0)
                {
                    $returns = $result->fetch_assoc();
                }
            }

            // Return either false or the row found
            return $returns;
        }
        /**
         * Selects all items with the given pageId from a given table
         * If no items are found, then returns false.
         * @param mixed $table
         * @param mixed $pageId
         * @return \array|boolean
         */
        public function selectItems($table, $pageId)
        {
            // Assume that there are no entries that correspond to the given page
            $returns = false;
            // An empty array for any entries that are returned later
            $rows = array();

            $table = $this->real_escape_string($table);

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
        /**
         * Selects a user from the DB based on a given username
         * If no user is found, then returns false.
         * @param mixed $username
         * @return \array|boolean
         */
        function selectUser($username)
        {
            // Prepare a statement
            $stmt = $this->prepare("SELECT * FROM User WHERE name=?");
            $returns = $stmt->bind_param("s",$username);

            if ($returns)
            {
                // Execute the statement
                $returns = $stmt->execute();

                if ($returns)
                {
                    // Get the result
                    $result = $stmt->get_result();

                    // Ensure there were rows returned
                    if ($result->num_rows > 0)
                    {
                        // get the first row
                        $returns = $result->fetch_assoc();
                    }
                }
            }
            return $returns;
        }




        // INSERT AND UPDATE
        /**
         * Updates a section in the DB. Returns whether or not the statement succeeded
         * @param mixed $pageId The page the section belongs to
         * @param mixed $itemIndex The index of the section
         * @param mixed $content The contents of the section
         * @param mixed $sectionId The if for the section
         * @return boolean
         */
        function updateSection($pageId, $itemIndex, $content, $sectionId)
        {
            // Prepare an update statement
            $stmt = $this->prepare(
                "UPDATE `Section` SET `pageId`=?,`itemIndex`=?,`content`=?
                    WHERE `sectionId`=?");

            // Bind parameters to the statement
            $result = $stmt->bind_param("iisi", $pageId, $itemIndex, $content, $sectionId);
            // Execute the statement
            if ($result)
            {
                $result = $stmt->execute();
            }

            // Returns whether or not the statement succeeded
            return $result;
        }
        /**
         * Attempts to update a headig in the DB. Returns whether or not the statement succeeded.
         * @param mixed $pageId The page the heading belongs to
         * @param mixed $itemIndex the index for the heading
         * @param mixed $content the contents of the heading
         * @param mixed $headingType the type of the heading (h1, h2, h3, etc)
         * @param mixed $headingId The id for the heading
         * @return boolean
         */
        function updateHeading($pageId, $itemIndex, $content, $headingType, $headingId)
        {
            // Prepare an update statement
            $stmt = $this->prepare(
                "UPDATE `Heading` SET `pageId`=?,`itemIndex`=?,`content`=?,`headingType`=?
                    WHERE `headingId`=?");

            // Bind the parameters
            $result = $stmt->bind_param("iisii", $pageId, $itemIndex, $content, $headingType, $headingId);
            // execute the statement
            if ($result)
            {
                $result = $stmt->execute();
            }

            return $result;
        }
        /**
         * Attempts to insert a new Section into the DB. Returns whether or not the statement succeeded.
         * @param mixed $pageId The page the Section belongs to
         * @param mixed $itemIndex the index for the Section
         * @param mixed $content the contents of the Section
         * @return boolean
         */
        function insertSection($pageId, $itemIndex, $content)
        {
            // Prepare an insert statement
            $stmt = $this->prepare("INSERT INTO Section (pageId, itemIndex, content) VALUES (?,?,?)");
            $result = $stmt->bind_param("iis", $pageId, $itemIndex, $content);

            // Execute the statement
            if ($result)
            {
                $result = $stmt->execute();
            }

            return $result;
        }
        /**
         * Attempts to insert a new heading. Returns whether or not the statement succeeded.
         * @param mixed $pageId The page the heading belongs to
         * @param mixed $itemIndex the index of the heading
         * @param mixed $content the contents of the heading
         * @param mixed $headingType the heading type (h1,h2,h3)
         * @return boolean
         */
        function insertHeading($pageId, $itemIndex, $content, $headingType)
        {
            $result = false;

            $stmt = $this->prepare("INSERT INTO Heading (pageId, itemIndex, content, headingType) VALUES (?,?,?,?)");
            $result = $stmt->bind_param("iisi", $pageId, $itemIndex, $content, $headingType);

            // Execute the statement
            if ($result)
            {
                $result = $stmt->execute();
            }

            // Return whether or not the statement succeeded
            return $result;
        }



        // DELETE
        /**
         * Attempts to delete a heading in the db
         * @param mixed $itemId The id of the heading to be deleted
         * @return boolean
         */
        function deleteHeading($itemId)
        {
            $result = false;

            // prepare a statement
            $stmt = $this->prepare("DELETE FROM Heading WHERE headingId = ?");
            $result = $stmt->bind_param("i", $itemId);

            // execute the statement
            if ($result)
            {
                $result = $stmt->execute();
            }

            // Return whether or not the statement succeeded.
            return $result;
        }
        /**
         * Attempts to delete a Section in the db
         * @param mixed $itemId the id of the section
         * @return boolean
         */
        function deleteSection($itemId)
        {
            // Prepare a statment
            $stmt = $this->prepare("DELETE FROM Section WHERE sectionId = ?");
            $result = $stmt->bind_param("i", $itemId);

            // Execute the statement
            if ($result)
            {
                $result = $stmt->execute();
            }

            // Return whether or not the statement succeeded
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