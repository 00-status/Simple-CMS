<?php
session_start();

use simpleCMS\DB\DBHelper;
use simpleCMS\DB\DBInfo;

spl_autoload_register(function($class)
{
    require_once '..\\..\\' . $class . '.php';
});

// If the user is already validated, then send them to the edit page
if ($_SESSION["validated"] == true)
{
	header("location: ..\\Edit\\edit.php");
}


// Variable to track if the user input is valid or not
// Assumed to be valid until proven invalid
$validated = true;
$errorMessage = <<<ET

            <div class="ui form error">
                <div class="ui error message">
                    <div class="header">Invalid Credentials</div>
                    <p>The username or password entered is incorrect.</p>
                </div>
            </div>
ET;


// Login Stuff
if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    // Input Validation
    $username = trim($_POST['user']);
    $password = trim($_POST['pass']);

    // Check if either the username or password is empty
    if (empty($username) || empty($password))
    {
        $validated = false;
    }


    // Validate the username
    if (strlen($username) < 3 || strlen($username) > 15)
    {
        $validated = false;
    }
    // Validate the password
    if (strlen($password) < 8 || strlen($password) > 30)
    {
        $validated = false;
    }

    if ($validated === true)
    {
        // Check if the user exists in the DB
        $inf = new DBInfo();
        $db = new DBHelper($inf->host(),$inf->username(),$inf->pass(),$inf->dbName(),$inf->port());

        // Attempt to get the user from the DB
        $user = $db->selectUser($username);
        // Close the DB
        $db->close();
        $db = null;

        // If the user was not found
        if ($user === false)
        {
            $validated = false;
        }
        else if ($user['name'] === $username && password_verify($password, $user["password"]))
        {
            // Set Session Cookie
            $_SESSION["validated"] = true;
            // Redirect to the edit page
            header("Location: ..\\Edit\\edit.php");
            die();
        }
        else
        {
            $validated = false;
        }
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>login</title>

    <!-- Import semantic ui and jquery -->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.3.1/semantic.min.css" />
    <!-- Import JQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <!-- Import Semantic JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.3.1/semantic.min.js"></script>
</head>
<body>
    <div class="ui menu"></div>

    <div class="ui raised padded text container segment">
        <form class="ui form" action="#" method="post">
            <?php if ($validated != true) { echo $errorMessage; } ?>
            <div class="field">
                <label>Username</label>
                <input type="text" name="user" placeholder="Username"
                       pattern=".{3,15}" required title="Your username must be between 3 and 15 characters." />
            </div>
            <div class="field">
                <label>Password</label>
                <input type="password" name="pass" placeholder="Password"
                       pattern=".{8,30}" required  title="Your password must be between 8 and 30 characters."/>
            </div>
            <button class="ui right primary button" type="submit">Login</button>
        </form>
    </div>
</body>
</html>