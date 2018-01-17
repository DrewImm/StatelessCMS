<?php

namespace Stateless;

/**
 * A single item in a Menu, represented by a font awesome icon
 */
class MenuIcon extends MenuItem {

    /**
     * Output the menu item markup to the current output buffer
     */
    public function show() {

        $this->label = 
            "<i class=\"" . $this->label . "\" " .
            "aria-hidden=\"true\"></i>";

        parent::show();

    }

};
