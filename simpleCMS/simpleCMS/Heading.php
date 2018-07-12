<?php

namespace simpleCMS
{
	/**
     * Holds the heading information for a page
	 *
	 * @version 1.0
	 * @author Liam
	 */
	class Heading extends Item
	{
        // The index for the heading
        private $index = null;

        // The text of the heading
        public $content = null;
        // The type of heading (h1.h2, etc)
        public $type = null;


        function __construct($index, $type=1, $content="")
        {
            $this->index = $index;
            $this->type = $type;
            $this->content = $content;
        }

        /**
         * Retrieves the index of the header
         * @return mixed
         */
        public function getIndex()
        {
            return $this->getIndex;
        }

        /**
         * Returns an html header tag with the appropriate attributes
         * @return string
         */
        public function display()
        {
            return <<<ET

        <h$this->type> $this->content </h$this->type>
ET;
        }


	}
}