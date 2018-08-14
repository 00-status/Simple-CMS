<?php
use simpleCMS\Pages\Page;

spl_autoload_register(function($class)
{
    require_once $class .'.php';
});

$page = new Page(1, true);

?>
<!DOCTYPE html>
<html>
<head>
    <?= $page->headingHtml(); ?>
</head>

<body>
    <?= $page->displayContents(); ?>
</body>
</html>