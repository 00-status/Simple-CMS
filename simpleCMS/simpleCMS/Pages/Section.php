<?php

namespace simpleCMS\Pages
{
	/**
	 * Holds the content and index of a single paragraph on a page
	 *
	 */
	class Section extends Item
	{
        protected $index = null;

        // The contents of the paragraph
        private $content = null;

        function __construct($index, $content)
        {
            $this->index = $index;
            $this->content = htmlentities($content);
        }

        public function getIndex()
        {
            return $this->index;
        }
        public function display($semanticUI)
        {
            // The html that will be returned
            $html = "";

            // Split the content into its separate line breaks
            $section = explode("\n",$this->content);

            // Add each individual paragraph to the html
            foreach ($section as $paragraph)
            {
                $html .= <<<ET

    <p>$paragraph</p>
ET;
            }


            // return the html
            return $html;
        }
	}
}