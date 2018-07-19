<?php

?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit</title>
    <!-- Import semantic ui and jquery -->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.3.1/semantic.min.css" />
    <!-- Import JQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <!-- Import Semantic JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.3.1/semantic.min.js"></script>

    <script src="edit.js"></script>
</head>
<body>
    <div class="ui menu"></div>
    <div class="ui container">
        <!-- Page Selection -->

        <div class="ui padded segment">
            <div id="pageArea" class="ui centered grid container">
            </div>
        </div>

        <!-- Area for adding Items -->
        <div id="addArea" class="ui segment">
            <button id="addHeading" class="ui black labeled icon button"><i class="plus icon"></i>Add Heading</button>
            <button id="addSection" class="ui primary labeled icon button"><i class="plus icon"></i>Add Section</button>
            <button id="savePage" class="ui green right labeled icon button"><i class="folder open icon"></i>Save</button>
        </div>


        <!-- Area for Items -->
        <div id="itemsArea" class="ui segment">

        </div>
    </div>
</body>
</html>