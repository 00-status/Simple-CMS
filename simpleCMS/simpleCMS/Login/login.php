<?php



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
        <form class="ui form" action="validate.php" method="post">
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