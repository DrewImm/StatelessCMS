<?php

namespace Stateless;

/**
 * @brief A single item in a Menu
 */
class MenuItem {
     public $label; /**< Human-readable text to display */
     public $href; /**< Path href the menu item should execute */
     public $tooltip; /**< Optional tooltip to attach to this menu item */
     public $tooltipLocation; /**< Optional tooltip location */
     public $attributes; /**< Array of additional attributes */
     public $id; /**< Optional id attribute for the item */
     public $class; /**< Optional CSS class to attach to this menu item */
     public $linkClass = false;

    /**
     * @brief Create a new menu item
     * @param mixed $label Human-readable text to display or Array of key
     *  value pairs
     * @param string $href Path href the menu item should execute
     * @param mixed $tooltip Tooltip string to use, or false for none
     * @param string $tooltipLocation Tooltip location.  Default is "bottom"
     * @param array $attributes Array of additional attributes
     * @param string $id Optional id attribute for the item
     * @param string $class Optional CSS class to attach to this menu item
    */
    public function __construct(
        $label,
        $href,
        $tooltip = false,
        $tooltipLocation = "bottom",
        $attributes = array(),
        $id = false,
        $class = false
    ) {
        
        // Check if first parameter is array
        if (is_array($label)) {
            $data = $label;

            if (array_key_exists("label", $data)) {
                $label = $data["label"];
            }

            if (array_key_exists("href", $data)) {
                $href = $data["href"];
            }

            if (array_key_exists("tooltip", $data)) {
                $tooltip = $data["tooltip"];
            }

            if (array_key_exists("tooltip_location", $data)) {
                $tooltipLocation = $data["tooltip_location"];
            }

            if (array_key_exists("attributes", $data)) {
                $attributes = $data["attributes"];
            }

            if (array_key_exists("id", $data)) {
                $id = $data["id"];
            }

            if (array_key_exists("class", $data)) {
                $class = $data["class"];
            }

            if (array_key_exists("link_class", $data)) {
                $linkClass = $data["link_class"];
            }
            
        }

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
        if ($this->label instanceof Menu) {
            $this->label->show();
        }
        else {
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
    
            // Active
            if ($this->href === Request::getPath() ||
                (
                    strpos(Request::getPath(), $this->href) !== false &&
                    $this->href !== "/"
                )) {
                $this->linkClass .= " active";
            }
    
            // Link
            echo "<a href=\"" . $this->href . "\" class=\"" . $this->linkClass . "\">";
    
            // Label
            echo $this->label;
    
            // Closeup
            echo "</a></li>";
        }
    }
}
