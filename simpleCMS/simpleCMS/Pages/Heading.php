<?php

namespace simpleCMS\Pages
{
	/**
     * Holds the heading information for a page
	 *
	 */
	class Heading extends Item
	{
        // The index for the heading
        private $index = null;

        // The text of the heading
        public $content = null;
        // The type of heading (h1.h2, etc)
        public $type = null;


        function __construct($index, $type, $content)
        {
            $this->index = $index;

            $this->type = htmlentities($type);

            $this->content = htmlentities($content);
        }

        /**
         * Retrieves the index of the header
         * @return mixed
         */
        public function getIndex()
        {
            return $this->index;
        }

        /**
         * Returns an html header tag with the appropriate attributes
         * @return string
         */
        public function display($semanticUI)
        {
            $returns = "";

            $returns = <<<ET

    <h$this->type> $this->content </h$this->type>
ET;

            return $returns;
        }
	}
}