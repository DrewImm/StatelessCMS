<?php

namespace Stateless;

/**
 * A Menu contains a list of MenuItem objects and build the markup
 */
class Menu {
    /** Array of MenuItem objects */
    public $items = array();

    /** Key/value pairs to attach to the tag */
    public $attributes = array();

    /**
     * Create a new Menu
     * 
     * @param array $items Array of MenuItem or MenuIcon objects
     * @param array $attributes Key/value pairs to attach to the tag
     */
    public function __construct($items = [], $attributes = []) {
        if (!empty($items)) {
            $this->items = $items;
        }
        
        if (!empty($attributes)) {
            $this->attributes = $attributes;
        }
    }

    /**
     * Output the menu and it's items to the current output buffer
     */
    public function show() {

        // Defaults
        if (!$this->attributes || !array_key_exists("class", $this->attributes)) {
            $this->attributes["class"] = "nav";
        }

        echo "<ul";

        // Output attributes
        foreach ($this->attributes as $key => $value) {
            echo " " . $key . "=\"" . $value . "\"";
        }

        echo ">";

        // Output MenuItems
        foreach ($this->items as $item) {
            $item->show();
        }

        // Closeup
        echo "</ul>";
    }

};
