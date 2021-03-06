﻿

var items = new Array();
var currentPageId = null;
var retrieveUrl = "retrieveData.php";

getPages();

// REQUEST FUNCTIONS
function getPages()
{
    var data = { data: "pages" };

    fetch(retrieveUrl, {
        method: "post",
        credentials: 'include',
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data)
    })
        .then(function (response) {
            return response.json();
        })
        .then(function (jsonData) {
            displayPages(jsonData);
        });
}
function getItems(pageId)
{
    currentPageId = pageId;
    // Retrieve headings
    var data = { data: "items", pageId: currentPageId };

    $.ajax({
        url: retrieveUrl,
        type: "POST",
        contentType: 'application/json',
        data: JSON.stringify(data),
        success: function (jsonData)
        {
            items = new Array();

            if (jsonData[0] != "no items")
            {
                // Add all items appropriately
                for (var i = 0; i < jsonData.length; i++)
                {
                    // If there is only one item of this type
                    if (jsonData[i].length == 1)
                    {
                        items.push(jsonData[i][0]);
                    }
                    else
                    {
                        for (var j = 0; j < jsonData[i].length; j++)
                        {
                            items.push(jsonData[i][j]);
                        }
                    }
                }

                // Sort items
                items.sort(itemCompare);
            }

            // Display the items on the page
            displayItems();
        }
    });
}
function saveItems()
{
    var saveUrl = "savePage.php";

    // If the items array has stuff in it, then save the current page's items
    if (items.length > 0)
    {
        // Remove any Image items without paths
        for (var i = 0; i < items.length; i++)
        {
            if (items[i].hasOwnProperty("path"))
            {
                if (items[i]["path"] === null)
                {
                    reblanceItems(index);
                }
            }
        }

        $.ajax({
            url: saveUrl,
            type: "POST",
            contentType: 'application/json',
            data: JSON.stringify(items),
            success: function (jsonData) {
                // Retrieve the items, so any items that just got an id are updated
                getItems(currentPageId);
            }
        });
    }
}



// ITEM FUNCTIONS
function itemCompare(a, b)
{
    var result = 0;

    if (a["itemIndex"] < b["itemIndex"])
    {
        result = -1;
    }
    else if (a["itemIndex"] > b["itemIndex"])
    {
        result = 1;
    }

    return result;
}
function itemSwap(index, newIndex)
{
    // Get the below item
    var temp = items[newIndex];

    // Change the itemIndex
    temp["itemIndex"] = index;

    // Change the below item to the current item
    items[newIndex] = items[index];
    // Change the itemIndex
    items[newIndex]["itemIndex"] = newIndex;

    // Complete the swap
    items[index] = temp;
}
function changeIndex(event, up)
{
    // Get the index from the id
    var changing = event.target.id

    changing = changing.split("-");
    var index = parseInt( changing[1] );

    // determine if the item is going up or down
    if (up && index != 0)
    {
        // Index of the element we're switching with
        var newIndex = index - 1;

        // Swap the two elements
        itemSwap(index, newIndex);

    }
    else if (up == false && index != items.length - 1)
    {
        // Index of the element we're switching with
        var newIndex = index + 1;
        itemSwap(index, newIndex);
    }

    // Sort the items
    items.sort(itemCompare);

    // Display the items
    displayItems();
}
function deleteItem(event)
{
    var delUrl = "delPage.php";

    var index = event.target.id;
    index = index.split("-");
    index = parseInt(index[1]);
   
    if (typeof items[index]["imageId"] !== 'undefined' || typeof items[index]["sectionId"] !== 'undefined' ||
        typeof items[index]["headingId"] !== 'undefined')
    {
        // Delete the item from the db
        $.ajax({
            url: delUrl,
            type: "POST",
            contentType: 'application/json',
            data: JSON.stringify(items[index]),
            success: function (jsonData)
            {
                saveItems();
            }
        });
    }

    reblanceItems(index);

    // display the items
    displayItems();
}
function reblanceItems(index)
{
    // Delete the item at the given index
    items.splice(index, 1);

    // change the itemIndex of each element located after the one just deleted.
    for (var i = index; i < items.length; i++) {
        items[i]["itemIndex"] = i;
    }
}
function addHeading()
{
    // Create a new Heading
    var newItem = { pageId: currentPageId, itemIndex: items.length, content: "new Title", headingType: "1", itemType: "Heading" };

    // Add the new item to the end of the items array
    items.push(newItem);

    // Display the new item
    displayItems();
}
function addSection()
{
    // Create a new Section
    var newItem = { pageId: currentPageId, itemIndex: items.length, content: "new Section", itemType: "Section" };

    // Add the section to the end of the items array
    items.push(newItem);

    // Display the new item
    displayItems();
}
function addImage()
{
    // Create a new image Item
    var newItem = { pageId: currentPageId, itemIndex: items.length, path: null, alt: "image", name: "", itemType: "Image" };

    // Add the image to the end of the items array
    items.push(newItem);

    // Display the new item.
    displayItems();
}
function uploadImage(event)
{
    // Get the index
    var itemId = event.target.id;
    var index = itemId.split("-");
    index = parseInt(index[1]);

    // Get the relavent item for later
    var currentItem = items[index];

    // Get the file ready for submission
    var imageInput = document.getElementById("image-" + index);
    var image = imageInput.files[0];
    var formdata = new FormData();
    formdata.append("file", image);


    $.ajax({
        url: 'uploadImage.php',
        type: 'POST',
        processData: false,
        contentType: false,
        dataType: 'json',
        data: formdata,
        success: function (data)
        {
            // Check if the upload was successful
            if (data["successPath"] != null)
            {
                // Set the path of the image item
                currentItem.path = data["successPath"];
                // Set the name of the image
                currentItem.name = data["successName"];
                // Save the new image
                saveItems();
            }
            else if (data["error"] != null)
            {
                // Create an error message
                var errorhtml = '<div class="ui negative message"><p>'+ data["error"] +'</p></div>';
                $("#imageError-" + index).html(errorhtml);

                // Display the error message
                $("#imageError-" + index).removeClass("hidden");
            }
        }
    });
}
function saveItemContent(event)
{
    var itemId = event.target.id;
    var index = itemId.split("-");
    index = parseInt(index[1]);
    
    // Set the content of the changed item
    items[index]["content"] = $("#" + itemId).val();
}
function saveHeadingType(event)
{
    var itemId = event.target.id;
    var index = itemId.split("-");
    index = parseInt(index[1]);

    // Set the heading type
    items[index]["headingType"] = $("#" + itemId).val();
}



// DISPLAY FUNCTIONS
function displayItems()
{
    // Clear any items that are currently displayed
    $("#itemsArea").html("");


    // Loop through the items
    for (var i = 0; i < items.length; i++)
    {
        // Item start html
        var htmlBegin = '<div class="ui container">' +
            '<div class="ui container">'+
                '<div class="ui blue buttons">'+
                    '<button id="' + 'up-' + items[i]["itemIndex"] + '" class="ui icon button">' +
                        '<i id="' + 'up-' + items[i]["itemIndex"] + '"class="arrow up icon"></i></button>' +
                    '<button id="' + 'down-' + items[i]["itemIndex"] + '" class="ui icon button">' +
                        '<i id="' + 'down-' + items[i]["itemIndex"] + '" class="arrow down icon"></i></button>' +
                '</div>'+
                '<button id="delete-'+ items[i]["itemIndex"] +'" class="ui right floated red icon button">'+
                    '<i id="delete-' + items[i]["itemIndex"] +'" class="close icon"></i>'+
                '</button>'+
            '</div>';

        // Item end html
        var htmlEnd = '</div>' +
                  '<div class="ui divider"></div>';


        if (items[i]['itemType'] == "Heading")
        {
            // Prepare the options for the heading type
            var options = new Array();

            // Prepare the options
            options[0] = '<option value="1">1</option>';
            options[1] = '<option value="2">2</option>';
            options[2] = '<option value="3">3</option>';
            options[3] = '<option value="4">4</option>';

            // Determine which option should be selected
            switch (parseInt(items[i]["headingType"]))
            {
                case 1:
                    options[0] = '<option selected="selected" value="1">1</option>';
                    break;
                case 2:
                    options[1] = '<option selected="selected" value="2">2</option>';
                    break;
                case 3:
                    options[2] = '<option selected="selected" value="3">3</option>';
                    break;
                case 4:
                    options[3] = '<option selected="selected" value="4">4</option>';
                    break;
                default:
            }

            // Create the heading
            $("#itemsArea").append(htmlBegin +
                '<div class="ui form">' +
                    '<div class="inline fields">' +
                        '<label>Heading</label>' +
                        '<div class="field">' +
                            '<input id="heading-'+ items[i]["itemIndex"] +'" type="text" placeholder="heading" value="'+ items[i]['content'] +'" />' +
                        '</div>' +
                        '<div>' +
                            '<select id="headingType-'+ items[i]["itemIndex"] +'" class="ui fluid dropdown">' +
                                options[0] +
                                options[1] +
                                options[2] +
                                options[3] +
                            '</select>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
                '' + htmlEnd);
            $("#headingType-" + items[i]["itemIndex"]).change(function () { saveHeadingType(event); });
            $("#heading-" + items[i]["itemIndex"]).change(function () { saveItemContent(event); });

        }
        else if (items[i]['itemType'] == "Section")
        {
            $("#itemsArea").append(htmlBegin +
                '<div class="ui form">'+
                    '<div class="ui field">'+
                        '<label>Contents</label>'+
                        '<textarea id="section-'+ items[i]["itemIndex"] +'">'+items[i]['content']+'</textarea>'+
                    '</div>'+
                '</div>' + htmlEnd);

            $("#section-" + items[i]["itemIndex"]).change(function () { saveItemContent(event); });
        }
        else if (items[i]['itemType'] == "Image")
        {
            // Check if the path is set
            if (items[i]['path'] == null)
            {
                // Display an upload form
                $("#itemsArea").append(htmlBegin +
                    '<div class="ui form">' +
                        '<div id="imageError-' + items[i]["itemIndex"] + '"></div>' +
                        '<div class="ui field">' +
                            '<input type="file" name="imageUpload" id="image-'+ items[i]['itemIndex'] +'">' +
                        '</div>' +
                    '<input class="ui submit button" type="button" value="Upload Image" id="imageSubmit-' + items[i]['itemIndex'] +'">' +
                    '</div>' + htmlEnd);
                // Set up event listener for the image upload
                $("#imageSubmit-" + items[i]["itemIndex"]).click(function () { uploadImage(event);} );
            }
            else // The path is set
            {
                // Display an image 
                $("#itemsArea").append(htmlBegin + '<img src="'+ items[i]["path"] +'" class="ui huge image">' + htmlEnd);
            }
        }


        // Arrow event Listeners
        $('#up-' + items[i]["itemIndex"]).click(function () { changeIndex(event, true) });
        $('#down-' + items[i]["itemIndex"]).click(function () { changeIndex(event, false) });

        // Delete button listener
        $('#delete-' + items[i]["itemIndex"]).click(function () { deleteItem(event) });
    }
}
function displayPages(jsonData)
{

    // Loop through the returned json
    for (var i = 0; i < jsonData.length; i++)
    {
        // Add a button to the page
        $("#pageArea").append(
            '<button id="' + jsonData[i]["pageId"] + '" class="ui teal button">' + jsonData[i]["title"] + '</button>');

        // Set up an event listener for the new button
        $("#" + jsonData[i]["pageId"]).click(function () { getItems(event.target.id); });
    }  
}


// EVENT LISTENERS
$(function()
{
    $("#addHeading").click(addHeading);
    $("#addSection").click(addSection);
    $("#addImage").click(addImage);
    $("#savePage").click(saveItems);
});


// TEST FUNCTIONS
function test(event) { alert("HAI :) " + event.target.id); }