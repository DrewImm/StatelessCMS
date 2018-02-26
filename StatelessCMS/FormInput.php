<?php

namespace Stateless;

/**
 * A single input in a Form
 */
class FormInput {

    /** Text label to display */
    public $label;

    /** Input slug (name) */
    public $slug;

    /** Input type - i.e. "text", "textarea", "visual", "toggle", "html" */
    public $type;

    /** Input children (select options, etc) */
    public $children;

    /** Custom html for a standard form */
    public $html;

    /** Input description */
    public $description;

    /** Input hint */
    public $hint;

    /** Input current value */
    public $value;

    /** Input default value (value sent if field is empty and not required) */
    public $defaultValue;

    /** Input placeholder */
    public $placeholder;

    /** Key/value pairs for the input */
    public $attributes = array();

    /** If the label should break line after */
    public $inlineLabel; // TODO - No longer in use, remove on next breaking

    /** If the field should break line after */
    public $inlineField; // TODO - No longer in use, remove on next breaking

    /** If the field should be required */
    public $required;

    /** If the field should be read-only */
    public $readonly;

    /** Callback function to validate this form input */
    public $validateCallback;

    /** Array of arguments to pass to validation callback */
    public $validateArguments;

    /** Callback function to filter this form input */
    public $filterCallback;

    /** Array of arguments to pass to filter callback */
    public $filterArguments;

    /** Minimum numerical value (for number inputs) */
    public $min;

    /** Maximum numerical value (for number inputs) */
    public $max;

    /** Minimum string-length */
    public $minLength;

    /** Maximum string-length */
    public $maxLength;

    private $isValid;

    /**
     * Construct a new FormInput object
     * 
     * @param array $data (Optional) Key/value pairs for the input
     */
    public function __construct($data = []) {

        // Check if $data is array
        if ($data && is_array($data)) {

            // Label
            if (array_key_exists("label", $data)) {
                $this->label = $data["label"];
            }

            // Slug
            if (array_key_exists("slug", $data)) {
                $this->slug = $data["slug"];
            }

            // Input Type
            if (array_key_exists("type", $data)) {
                $this->type = $data["type"];
            }

            // Input children
            if (array_key_exists("children", $data)) {
                $this->children = $data["children"];
            }

            // Custom html
            if (array_key_exists("html", $data)) {
                $this->html = $data["html"];
            }

            // Description
            if (array_key_exists("description", $data)) {
                $this->description = $data["description"];
            }

            // Hint
            if (array_key_exists("hint", $data)) {
                $this->hint = $data["hint"];
            }

            // Value
            if (array_key_exists("value", $data)) {
                $this->value = $data["value"];
            }

            // Value
            if (array_key_exists("default_value", $data)) {
                $this->defaultValue = $data["default_value"];
            }

            // Placeholder
            if (array_key_exists("placeholder", $data)) {
                $this->placeholder = $data["placeholder"];
            }

            // Default Value
            if (array_key_exists("default_value", $data)) {
                $this->defaultValue = $data["default_value"];
            }

            // Attributes
            if (array_key_exists("attributes", $data)) {
                $this->attributes = $data["attributes"];
            }

            // Inline field
            if (array_key_exists("inline_field", $data)) {
                $this->inline_field = $data["inline_field"];
            }

            // Inline label
            if (array_key_exists("inline_label", $data)) {
                $this->inline_label = $data["inline_label"];
            }

            // Required
            if (array_key_exists("required", $data)) {
                $this->required = $data["required"];
            }

            // Read only
            if (array_key_exists("readonly", $data)) {
                $this->inline_label = $data["readonly"];
            }

            // Validate callback
            if (array_key_exists("validate", $data)) {
                $this->validateCallback = $data["validate"];
            }

            // Validate callback arguments
            if (array_key_exists("validate_arguments", $data)) {
                $this->validateArguments = $data["validate_arguments"];
            }

            // Filter callback
            if (array_key_exists("filter", $data)) {
                $this->filterCallback = $data["filter"];
            }

            // Filter callback
            if (array_key_exists("filter_arguments", $data)) {
                $this->filterArguments = $data["filter_arguments"];
            }

            // Minimum numerical value
            if (array_key_exists("min", $data)) {
                $this->min = $data["min"];
            }

            // Maximum numerical value
            if (array_key_exists("max", $data)) {
                $this->max = $data["max"];
            }

            // Minimum string-length
            if (array_key_exists("minlength", $data)) {
                $this->minLength = $data["min_length"];
            }

            // Maximum string-length
            if (array_key_exists("maxlength", $data)) {
                $this->maxLength = $data["maxlength"];
            }        
            
        }

    }

    /**
     * Get the input field's value from a request
     * 
     * @return mixed Returns the value from the array, or `false` if not set
     */
    public function getValue() {

        // Check if the request has the value
        if ($this->hasValue()) {

            // Return the payload value
            return Request::getPayload()[$this->slug];
        }
        else {

            // Return false
            return false;
        }

    }

    /**
     * Check if the input has value in a request
     * 
     * @return boolean `true` if the input has value, otherwise `false`
     */
    public function hasValue() {
        $payload = Request::getPayload();

        return (
            !empty($payload) &&
            isset($payload[$this->slug])
        );
    }

    /**
     * Set the value for this input
     * 
     * @param mixed $value New value for this 
     */
    public function setValue($value) {

        $this->value = $value;
        Request::setPayloadKey($this->slug, $value);

    }
    
    /**
     * Output the input markup to the current output buffer
     */
    public function show() {

        if (!array_key_exists("class", $this->attributes)) {
            $this->attributes["class"] = "form-control";
        }

        // Append valid/invalid class
        if ($this->isValid !== null) {

            if ($this->isValid) {
                $this->attributes["valid"] = "true";
                $this->attributes["class"] .= " is-valid";
            }
            else {
                $this->attributes["invalid"] = "true";
                $this->attributes["class"] .= " is-invalid";
            }

        }

        // Attributes to be passed to the input
        $attributes = "";

        // Convert "toggle" field to a standard checkbox
        if ($this->type === "toggle") {
            $this->defaultValue = 1;
            $this->type = "checkbox";

            if (intval($this->value) === intval($this->defaultValue)) {
                $this->attributes["checked"] = true;
            }
        }

        // Check for submission value
        if ($this->hasValue()) {
            $this->value = $this->getValue();
        }

        // Check value
        if (empty($this->value) && isset($this->defaultValue)) {
            $this->value = $this->defaultValue;
        }


        // Clean value for output
        if ($this->type !== "html" && $this->type !== "file") {
            $this->value = htmlspecialchars($this->value);
        }

        // Push slug to attributes
        if (!empty($this->slug)) {
            $this->attributes["id"] = rtrim("_" . $this->slug, "[]");
            $this->attributes["name"] = $this->slug;
        }

        // Push placeholder
        if ($this->placeholder) {
            $this->attributes["placeholder"] = $this->placeholder;
        }

        // Push required to attributes
        if ($this->required) {
            $this->attributes["required"] = true;
        }

        // Push readonly to attributes
        if ($this->readonly) {
            $this->attributes["readonly"] = true;
        }

        // Push min to attributes
        if (isset($this->min)) {
            $this->attributes["min"] = $this->min;
        }

        // Push max to attributes
        if (isset($this->max)) {
            $this->attributes["max"] = $this->max;
        }

        // Push minLength to attributes
        if (isset($this->minLength)) {
            $this->attributes["minlength"] = $this->minLength;
        }

        // Push maxLength to attributes
        if (isset($this->maxLength)) {
            $this->attributes["maxlength"] = $this->maxLength;
        }

        // Append attributes array to attributes string
        foreach ($this->attributes as $key => $value) {
            if ($value === true) {
                $attributes .= " " . $key;
            }
            else {
                $attributes .= " " . $key . "=\"" . $value . "\"";
            }
        }

        // Decode value HTML special chars
        if ($this->type !== "file") {

            $this->value = htmlspecialchars_decode($this->value);
            
        }

        // Output label
        if (!empty($this->label)) {
            echo sprintf(
                "<label for=\"_%s\">%s</label>",
                $this->slug,
                $this->label
            );

            // Output description
            if ($this->description) {
                echo "<p class=\"description\">" . $this->description . "</p>";
            }


        }

        // Output
        if ($this->html) {
            // Output custom HTML for field
            echo $this->html;
        }
        else {
            // Output standard fields
            switch ($this->type) {
                case "textarea":
                case "visual":
                    // Output editor
                    echo sprintf(
                        "<textarea %s>%s</textarea>",
                        $attributes,
                        $this->value
                    );
                break;
    
                case "select":
                    // Output select tag open
                    echo sprintf("<select %s>", $attributes);
    
                    // Output children
                    if (!empty($this->children) && is_array($this->children)) {
                        foreach ($this->children as $key => $value) {
                            if ((string) $value === (string) $this->value) {
                                echo sprintf(
                                    "<option value=\"%s\" selected>%s</option>",
                                    $value,
                                    $key);
                            }
                            else {
                                echo sprintf(
                                    "<option value=\"%s\">%s</option>",
                                    $value,
                                    $key
                                );
                            }
                        }
                    }
    
                    // Output closing
                    echo "</select>";
                break;
    
                case "html":
                    echo $this->value;
                break;
    
                case "file":
                    // Output standard input
                    echo sprintf(
                        "<input type=\"%s\" %s />",
                        $this->type,
                        $attributes
                    );
                break;
    
                default:
                    // Output standard input
                    echo sprintf(
                        "<input type=\"%s\" value=\"%s\" %s />",
                        $this->type,
                        $this->value,
                        $attributes
                    );
                break;
            }
        }

        // Display hint
        if ($this->isValid === false && $this->hint) {

            echo "<p class=\"hint\">" . $this->hint . "</p>";

        }

    }

    /**
     * Output the input's script to the current output buffer
     */
    public function showScript() {
        switch ($this->type) {
            case "visual":
                // Output CKeditor script
                echo
                    "<script>CKEDITOR.replace(\"" .
                    $this->slug .
                    "\");</script>"
                ;
            break;
        }
    }


    /**
     * Check if the input is valid
     * 
     * @return boolean Returns if the input field is valid
     */
    public function isValid() {

        // TODO - REmove
        if ($this->slug === "location") {
            $break = true;
        }

        // Create name for errors
        $name = ($this->label) ? $this->label : 
            ucwords(str_replace("_", " ", $this->slug));

        // Check if it's a button
        if ($this->type === "submit" || $this->type === "button") {
            return true;
        }

        // If not required and not filled out, it is valid
        if (!$this->required && 
            (empty($this->getValue()) && empty($_FILES))) {
            
            return true;

        }

        // If is required and not filled out, it is NOT valid
        if ($this->required && $this->getValue() !== "0" &&
            empty($this->getValue())) {

            $this->isValid = false;
            return $name . " is required.";
        }

        // At this point, if it has no value, it is not valid
        if (!$this->hasValue() && empty($_FILES)) {

            $this->isValid = false;
            return $name . " was not found.";
        }

        // Pull the value and the length

        $value = $this->type === "file" ? $_FILES : $this->getValue();

        // Run validate function
        if (!empty($value)) {
            
            if ($this->validateCallback) {
                if (!($this->validateCallback)($value, $this->validateArguments)) {

                    $this->isValid = false;

                    return $name . " is not valid.";
                }
            }

        }

        // String checks
        if (is_string($value)) {
            // Find string length
            $length = strlen($value);
    
            // Check minLength
            if (isset($this->minLength) && $length < $this->minLength) {

                $this->isValid = false;
                return $name . " must be at least " .
                    $this->minLength .
                    " characters long."
                ;
            }

            // Check maxLength
            if (isset($this->maxLength) && $length > $this->maxLength) {

                $this->isValid = false;
                return $name . " must not be longer than " .
                    $this->maxLength .
                    " characters."
                ;
            }
        }

        // Numerical checks
        if (is_numeric($value)) {
            $value = intval($value);

            // Check minLength
            if (isset($this->min) && $value < $this->min) {

                $this->isValid = false;
                return $name . " must be at least " . $this->min . ".";
            }

            // Check maxLength
            if (isset($this->max) && $value > $this->max) {

                $this->isValid = false;
                return $name . " must not be larger than " . $this->max . ".";
            }
        }

        // All checks passed

        $this->isValid = true;

        // Run filter function
        if ($this->filterCallback) {
            $this->setValue(
                ($this->filterCallback)($value, $this->filterArguments)
            );
        }

        // Return true
        return true;

    }

};
