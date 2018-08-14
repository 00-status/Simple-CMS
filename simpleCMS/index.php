<?php

use simpleCMS\Pages\Page;

spl_autoload_register(function($class)
{
    require_once $class .'.php';
});

$page = new Page(3, true);

?>

<!DOCTYPE html>
<html>
<head>
    <?= $page->headingHtml(); ?>
</head>
<body>

    <div class="ui menu"></div>
    <?= $page->displayContents(); ?>
</body>
</html>


