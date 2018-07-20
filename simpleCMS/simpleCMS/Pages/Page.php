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
	 * Page short summary.
	 *
	 * Page description.
	 *
	 * @version 1.0
	 * @author Liam
	 */
	class Page
	{

        public $title = null;

        /**
         * An array of items on the page
         * @var mixed
         */
        private $items = array();

        /**
         * Optionally takes in the title of the page as well as the current
         * count of how many items are on the page
         * @param mixed $title The main title of the page
         * @param mixed $itemCount The number of items on the page
         */
        function __construct($pageId)
        {
            // Get the specified page from the db
            $this->getPage($pageId);

        }

        /**
         * Adds a Section to the list of items for the page
         * @param mixed $index the index of the section on the page
         * @param mixed $content the text of the section
         */
        public function addSection($index, $content)
        {
            // Push a new Section onto the end of the array
            $this->items["$index"] = new Section($index, $content);
        }
        public function addHeading($index, $headingType, $content)
        {
            // Push a new Heading onto the end of the array
            $this->items["$index"] = new Heading($index, $headingType, $content);
        }

        public function removeItem($index)
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
        /**
         * Puts the sections, headings and widgets for this page into a string
         * @return string returns the formatted html
         */
        public function displayContents()
        {
            $html = "<div>";

            foreach ($this->items as $section)
            {
            	$html .= $section->display();
            }

            $html .= "</div>";
            return $html;
        }


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

            // Close the db
            $db->close();

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