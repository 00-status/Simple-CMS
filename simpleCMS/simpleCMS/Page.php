<?php

namespace simpleCMS
{
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
        /**
         * The number of items on the current page
         * @var mixed
         */
        public $itemCount;

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
        function __construct($title="", $itemCount=1)
        {
            // Set the title
            $this->title = new Heading(0, 1, $title);
            // Set the itemCount
            $this->itemCount = $itemCount;
        }

        public function setTitle($content)
        {
            $this->title->content = $content;
        }
        public function displayTitle()
        {
            return $this->title->display();
        }

        /**
         * Adds a paragraph to the list of paragraphs for the page
         * @param mixed $index the index of the paragraph on the page
         * @param mixed $content the text of the paragraph
         */
        public function addParagraph($content, $index=-1)
        {
            // If the user didn't specify an index then set it to be the itemCount
            // increment the itemcount
            if ($index == -1)
            {
                $index = $this->itemCount;
            }

            // Push a new paragraph onto the end of the array
            $this->items["$index"] = new Paragraph($index, $content);

            $this->itemCount++;
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
         * Puts the paragraphs, headings and widgets for this page into a string
         * @return string returns the formatted html
         */
        public function displayContents()
        {
            $html = "<div>";

            foreach ($this->items as $paragraph)
            {
            	$html .= $paragraph->display();
            }

            $html .= "</div>";
            return $html;
        }
	}
}