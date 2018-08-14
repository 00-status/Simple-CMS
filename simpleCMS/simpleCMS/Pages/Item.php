<?php

namespace simpleCMS\Pages
{
    /**
     * An abstract class that is the basis for various widgets
     *
     */
    abstract class Item
    {
        abstract public function getIndex();
        abstract public function display($semanticUI);


        public function decrementIndex()
        {
            $this->index--;
        }
    }
}