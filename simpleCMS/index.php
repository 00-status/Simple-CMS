<?php

use simpleCMS\Page;
use simpleCMS\Heading;

spl_autoload_register(function($class)
{
    require_once $class .'.php';
});

$page = new Page();
$page->setTitle("Example Page");

$page->addParagraph("First Paragraph",1);
$page->addParagraph("Second Paragraph",2);
$page->addParagraph("Third Paragraph",3);
$page->addParagraph("Fourth Paragraph",4);
$page->addParagraph("Fifth Paragraph $page->itemCount");

$page->removeItem(2);

?>

<html>
<head>
    <title>
        <?= $page->title->content; ?>
    </title>
</head>
<body>
    <?= $page->displayTitle(); ?>

    <?= $page->displayContents(); ?>

</body>
</html>


