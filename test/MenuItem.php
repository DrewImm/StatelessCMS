<?php

namespace Stateless;

/**
 * @brief A single item in a Menu
 */
class MenuItem {
     public $label; /**< Human-readable text to display */
     public $href; /**< Path href the menu item should execute */
     public $id; /**< Optional #id tag for the item */
     public $class; /**< Optional CSS class to attach to this menu item */
     public $tooltip; /**< Optional tooltip to attach to this menu item */
     public $tooltipLocation; /**< Optional tooltip location */
     public $attributes; /**< Array of additional attributes */

    /**
     * @brief Create a new menu item
     * @param string $label Human-readable text to display
     * @param string $href Path href the menu item should execute
     * @param string $id Optional #id tag for the item
     * @param string $class Optional CSS class to attach to this menu item
     * @param string $tooltip Optional tooltip to attach to this menu item
     * @param string $tooltipLocation Optional tooltip location
     * @param array $attributes Array of additional attributes
    */
    public function __construct(
        $label,
        $href,
        $tooltip = false,
        $tooltipLocation = "bottom",
        $id = false,
        $class = false,
        $attributes = array()
    ) {
        $this->label = $label;
        $this->href = $href;
        $this->id = $id;
        $this->class = $class;
        $this->tooltip = $tooltip;
        $this->tooltipLocation = $tooltipLocation;
        $this->attributes = $attributes;
    }

    /**
     * @brief Output the menu item markup to the current output buffer
     */
    public function show() {
        echo "<li";

        // Id
        if (!empty($this->id)) {
            echo " id=\"" . $this->id . "\"";
        }

        // Class
        if (!empty($this->class)) {
            echo " class=\"" . $this->class . "\"";
        }

        // Tooltip
        if (!empty($this->tooltip)) {
            echo 
                " data-toggle=\"tooltip\"" .
                " data-placement=\"" . $this->tooltipLocation . "\"" .
                " title=\"" . $this->tooltip . "\""
            ;
        }

        //  Attributes
        if (!empty($this->attributes)) {
            foreach ($this->attributes as $key => $value) {
                echo " " . $key . "=\"" . $value . "\"";
            }
        }

        echo ">";

        // Link
        echo "<a href=\"" . $this->href . "\">";

        // Label
        echo $this->label;

        // Closeup
        echo "</a><li>";
    }
}