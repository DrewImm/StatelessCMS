<?php

namespace Stateless;

/**
 * A single item in a Menu
 */
class MenuItem {

    /** Human-readable text to display */
    public $label;

    /** Path href the menu item should execute */
    public $href;

    /** Optional tooltip to attach to this menu item */
    public $tooltip;

    /** Optional tooltip location */
    public $tooltipLocation = "top";

    /** Array of additional attributes */
    public $attributes;

    /** Array of additional attributes for the link */
    public $linkAttributes;

    /** Menu object as a nested submenu */
    public $submenu;

    /** Broad Active allows the menu item to receive .active even if the URL
     *      isn't an exact match
     */
    public $broadActive = false; 

    /**
     * Create a new menu item
     * 
     * @param string $label Menu label text/html
     * @param string $href Menu link path
     * @param array $data (Optional) Data to pass to the menu item
    */
    public function __construct($label = false, $href = false, $data = []) {

        // Check if an array was passed as the label
        if (is_array($label)) {
            $data = $label;
            $label = false;
        }

        $this->label = $label;
        $this->href = $href;

        // Check if $data is array
        if ($data && is_array($data)) {

            // Label
            if (array_key_exists("label", $data)) {
                $this->label = $data["label"];
            }

            // Href
            if (array_key_exists("href", $data)) {
                $this->href = $data["href"];
            }

            // Tooltip
            if (array_key_exists("tooltip", $data)) {
                $this->tooltip = $data["tooltip"];
            }

            // Tooltip location
            if (array_key_exists("tooltip_location", $data)) {
                $this->tooltipLocation = $data["tooltip_location"];
            }

            // Attributes
            if (array_key_exists("attributes", $data)) {
                $this->attributes = $data["attributes"];
            }

            // Link attributes
            if (array_key_exists("link_attributes", $data)) {
                $this->linkAttributes = $data["link_attributes"];
            }

            // Nested submenu
            if (array_key_exists("submenu", $data)) {
                $this->submenu = $data["submenu"];
            }

            // Broad active match
            if (array_key_exists("broad_active", $data)) {
                $this->broadActive = $data["broad_active"];
            }

        }

    }

    /**
     * Output the menu item markup to the current output buffer
     */
    public function show() {

        // Defaults

        // Class
        if (!is_array($this->attributes) ||
            !array_key_exists("class", $this->attributes)) {
        
            // Set default class
            $this->attributes["class"] = "nav-item";
        }

        // Link class
        if (!is_array($this->linkAttributes) ||
            !array_key_exists("class", $this->linkAttributes)) {
        
            // Set default class
            $this->linkAttributes["class"] = "nav-link";
        }

        // Output list item
        echo "<li";

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

        // TODO - Remove
        if ($this->href === "/messages/inbox-trash") {

           $break = true; 

        }

        // Check if this link matches the request path
        $activeClass = false;
        if ($this->href === Request::getPath() ||
            (
                $this->href &&
                $this->href !== "/" &&
                $this->broadActive &&
                strpos(Request::getPath(), $this->href) !== false
            )) {
                
            if (!$this->linkAttributes ||
                !array_key_exists("class", $this->attributes)) {

                // Create class attribute
                $this->linkAttributes["class"] = "active";
            }
            else {

                // Update class attribute
                $this->linkAttributes["class"] .= " active";
            }
        }

        // Output link
        echo "<a href=\"" . $this->href . "\"";
        
        foreach ($this->linkAttributes as $key => $value) {
            echo " " . $key . "=\"" . $value . "\"";
        }
        
        echo ">";

        // Label
        echo $this->label;

        // Close link
        echo "</a>";

        // Check for nested menus
        if ($this->submenu && $this->submenu instanceof Menu) {
            $this->submenu->show();
        }

        // Closeup
        echo "</li>";

    }

};
