<?php

use simpleCMS\Pages\Page;

spl_autoload_register(function($class)
{
    require_once $class .'.php';
});

$page = new Page(1);

?>

<!DOCTYPE html>
<html>
<head>
    <title> <?= $page->title; ?> </title>

    <!-- Import semantic ui and jquery -->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.3.1/semantic.min.css" />
    <!-- Import JQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <!-- Import Semantic JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.3.1/semantic.min.js"></script>
</head>
<body>

    <div class="ui menu"></div>
    <div class="ui text container">
        <?= $page->displayContents(); ?>
    </div>
</body>
</html>


