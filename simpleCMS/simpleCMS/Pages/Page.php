<?php


namespace simpleCMS\Pages
{
    use simpleCMS\DB\DBHelper;
    use simpleCMS\DB\DBInfo;

    spl_autoload_register(function($class)
    {
        require_once '..\\..\\' . $class . '.php';
    });
	/**
	 * Holds and displays information relating to a page,
     * such as headings images and sections.
	 */
	class Page
	{
        /**
         * indicates whether or not to use the SemanticUI Framework. Defaults to false
         * @var bool
         */
        private $semanticUI;
        public $title = "";

        /**
         * An array of items on the page
         * @var mixed
         */
        private $items = array();

        /**
         * Takes in the pageId and whether or not to use the semantic ui framework.
         * Retrieves the page information and prepares for display
         * @param mixed $pageId the id of the page to be displayed
         * @param mixed $semanticUI indicates whether or not to use the semantic ui framework
         */
        function __construct($pageId, $semanticUI=false)
        {
            $this->semanticUI = $semanticUI;

            // Check if this pageId is already in the Database
            $inf = new DBInfo();
            $db = new DBHelper($inf->host(),$inf->username(),$inf->pass(),$inf->dbName(),$inf->port());
            $exists = $db->pageExists($pageId);

            if ($exists)
            {
                // Load the specified page from the db
                $this->getPage($pageId);
            }
            else
            {
                $db->insertPage($pageId);
            }
        }

        /**
         * Adds a Section to the list of items for the page
         * @param mixed $index the index of the section on the page
         * @param mixed $content the text of the section
         */
        private function addSection($index, $content)
        {
            // Push a new Section onto the end of the array
            $this->items["$index"] = new Section($index, $content);
        }
        private function addHeading($index, $headingType, $content)
        {
            // Push a new Heading onto the end of the array
            $this->items["$index"] = new Heading($index, $headingType, $content);
        }
        private function addImage($index, $path, $alt)
        {
            // Push a new Heading onto the end of the array
            $this->items["$index"] = new Image($index, $path, $alt);
        }

        private function removeItem($index)
        {
            // Attempt to find the requested index
            if (array_key_exists($index, $this->items))
            {
            	// Remove the item
                array_splice($this->items, $index, 1);

                // Change index in each item
                for ($i = $index; $i < count($this->items); $i++)
                {
                    $this->items["$i"]->decrementIndex();
                }
            }
        }

        // DISPLAY FUNCTIONS
        /**
         * Displays header meta information like the page title
         * If semantic ui is enabled then jQuery and semantic ui will be imported
         * @return string Returns the header html
         */
        public function headingHtml()
        {
            $html = "<title>$this->title</title>";
            if ($this->semanticUI)
            {
            	$html .= <<<ET

    <!-- Import semantic ui and jquery -->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.3.1/semantic.min.css" />
    <!-- Import JQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <!-- Import Semantic JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.3.1/semantic.min.js"></script>
ET;
            }

            return $html;
        }
        /**
         * Puts the sections, headings and widgets for this page into an html string
         * @return string returns the formatted html
         */
        public function displayContents()
        {
            // If semantic is enabled, then add some classes to the overarching div
            if ($this->semanticUI)
            {
                $html = <<<ET

    <div class="ui text container">
ET;
            } else {
                $html = <<<ET

    "<div>"
ET;
            }

            foreach ($this->items as $section)
            {
            	$html .= $section->display($this->semanticUI);
            }

            $html .= <<<ET

    </div>
ET;
            return $html;
        }



        // DB FUNCTIONS
        private function getPage($pageId)
        {
            $inf = new DBInfo();
            $db = new DBHelper($inf->host(),$inf->username(),$inf->pass(),$inf->dbName(),$inf->port());


            $currentPage = $db->selectPage($pageId);
            if ($currentPage != false && !empty($currentPage))
            {
                $this->title = $currentPage["title"];
            }

            // Get items for the page
            // Sections
            $sections = $db->selectItems("Section", $pageId);
            if ($sections != false && !empty($sections))
            {
                foreach ($sections as $section)
                {
                    $this->addSection($section["itemIndex"], $section["content"]);
                }

            }
            // Headings
            $headings = $db->selectItems("Heading", $pageId);
            if ($headings != false && !empty($headings))
            {
                foreach ($headings as $heading)
                {
                    $this->addHeading($heading["itemIndex"], $heading["headingType"], $heading["content"]);
                }
            }
            // Images
            $images = $db->selectItems("Image", $pageId);
            if ($images != false && !empty($images))
            {
                foreach ($images as $image)
                {
                    $this->addImage($image["itemIndex"], $image["path"], $image["alt"]);
                }
            }

            // Close the db
            $db->close();
            $db = null;

            // Sort the items for the page
            if (!empty($this->items))
            {
                usort($this->items, function($item1, $item2) {
                    return $item1->getIndex() <=> $item2->getIndex();
                });
            }
        }
	}
}