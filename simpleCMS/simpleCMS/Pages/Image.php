<?php

namespace simpleCMS\Pages
{
    /**
     * Holds the heading information for a page
     *
     */
    class Image extends Item
    {
        // The index for the image
        private $index;

        // The src path for the image
        private $path;
        // The alt tag of the image
        private $alt;

        function __construct($index, $path, $alt)
        {
            $this->index = $index;

            $this->path = htmlentities($path);

            $this->alt = htmlentities($alt);
        }

        /**
         * Retrieves the index of the Image
         * @return mixed
         */
        public function getIndex()
        {
            return $this->index;
        }

        /**
         * Returns an html img tag with the appropriate attributes
         * @return string
         */
        public function display($semanticUI)
        {
            $returns = "";

            if ($semanticUI)
            {
            	$returns = <<<ET

    <div class="ui basic segment">
        <img class="ui rounded image" alt="$this->alt" src="$this->path" />
    </div>
ET;
            }
            else
            {
                $returns =             <<<ET

        <img align="middle" alt="$this->alt" src="$this->path" />
ET;
            }
            return $returns;
        }
    }
}