<?php

namespace simpleCMS
{
	/**
	 * Holds the content and index of a single paragraph on a page
	 *
	 * @version 1.0
	 * @author Liam
	 */
	class Paragraph extends Item
	{
        protected $index = null;

        // The contents of the paragraph
        private $content = null;

        function __construct($index, $content="")
        {
            $this->index = $index;
            $this->content = $content;
        }

        public function getIndex()
        {
            return $this->index;
        }
        public function display()
        {
            return <<<ET

            <p>$this->content</P>
ET;
        }
	}
}