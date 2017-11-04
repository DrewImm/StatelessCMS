<?php

namespace Stateless;

/**
 * @brief A Menu contains a list of MenuItem objects and build the markup
 */
class Menu {
    public $items = array(); /**< Array of MenuItem objects */
    public $attributes = array(); /**< Key/value pairs to attach to the tag */

    /**
     * @brief Create a new Menu
     * @param array $items Array of items
     * @param array $attributes Key/value pairs to attach to the tag
     */
    public function __construct($items, $attributes) {
        $this->items = $items;
        $this->attributes = $attributes;
    }

    /**
     * @brief Output the menu and it's items to the current output buffer
     */
    public function show() {
        echo "<ul";

        // Output attributes
        foreach ($this->attributes as $key => $value) {
            echo " " . $key . "=\"" . $value . "\"";
        }

        // Output MenuItems
        foreach ($this->items as $item) {
            $item->show();
        }

        // Closeup
        echo "</ul>";
    }

}