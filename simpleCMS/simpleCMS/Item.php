<?php

namespace simpleCMS
{
    /**
     * An abstract class that is the basis for various widgets
     *
     * @version 1.0
     * @author Liam
     */
    abstract class Item
    {
        abstract public function getIndex();
        abstract public function display();


        public function decrementIndex()
        {
            $this->index--;
        }
    }
}