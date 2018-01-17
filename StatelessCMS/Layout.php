<?php

namespace Stateless;

/**
 * Create a HTML Layout
 */
class Layout {
    
    /**
     * Get the HTML markup for this Layout
     *
     * @return string Returns the HTML markup
     */
    public function get() {
        throw new \Exception
            ("Layout::get() called, but child has no get method!");

        return "";
    }

    /**
     * Get the HTML markup to close this Layout
     *
     * @return string Returns the HTML markup
     */
    public function getClose() {
        throw new \Exception
            ("Layout::getClose() called, but child has no getClose method!");

        return "";
    }

    /**
     * Output the HTML markup to the current output buffer
     * 
     * @return Layout Returns $this
     */
    public function show() {
        echo $this->get();

        return $this;
    }

    /**
     * Close this Layout
     * 
     * @return Layout Returns $this
     */
    public function close() {
        echo $this->getClose();

        return $this;
    }

};
