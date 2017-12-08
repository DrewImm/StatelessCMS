<?php

namespace Stateless;

/**
 * @brief A single input in a Form
 */
class FormInput {
    public $label; /**< Text label to display */
    public $slug; /**< Input slug (name) */
    public $type; /**< Input type - i.e. "text", "textarea", "visual", "toggle" */
    public $value; /**< Input current value */
    public $defaultValue; /**< Input default value */
    public $attributes = array(); /**< Key/value pairs for the input */
    public $inlineLabel = false; /**< If the label should break line after */
    public $inlineField = false; /**< If the field should break line after */

    public $accepts; /**< Regex type this input accepts */
    public $min; /**< Minimum numerical value */
    public $max; /**< Maximum numerical value */
    public $minLength; /**< Minimum string-length */
    public $maxLength; /**< Maximum string-length */
    public $required; /**< If the field should be required */
    public $readonly; /**< If the field should be read-only */

    /**
     * @brief Construct a new FormInput object
     * @param string $label Label for the input
     * @param string $slug Input slug (name)
     * @param string $type Input type - i.e. "text", "textarea", "visual",
     *  "toggle".  Default is "text"
     * @param boolean $value Input current value.  Default is empty
     * @param boolean $defaultValue Input default value.  Default is empty
     * @param array $attributes Key/value pairs for the input.  Default is empty
     */
    public function __construct(
        $label,
        $slug,
        $type = "text",
        $value = false,
        $defaultValue = false,
        $attributes = array()
    ) {
        $this->label = $label;
        $this->slug = $slug;
        $this->type = $type;
        $this->value = $value;
        $this->defaultValue = $defaultValue;

        // Check for custom attributes
        if (is_array($attributes)) {

            // Inline Label
            if (array_key_exists("inlineLabel", $attributes)) {
                $this->inlineLabel = $attributes["inlineLabel"];
                unset($attributes["inlineLabel"]);
            }

            // Inline Field
            if (array_key_exists("inlineField", $attributes)) {
                $this->inlineField = $attributes["inlineField"];
                unset($attributes["inlineField"]);
            }

        }


        $this->attributes = $attributes;
    }

    /**
     * @brief Output the input markup to the current output buffer4
     */
    public function show() {
        $attributes = "";

        // Convert "toggle" field to normal checkbox
        if ($this->type === "toggle") {
            $this->defaultValue = 1;
            $this->type = "checkbox";

            if (intval($this->value) === inval($this->defaultValue)) {
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
        if ($this->type !== "html") {
            $this->value = htmlspecialchars($this->value);            
        }

        // Push slug to attributes
        if (!empty($this->slug)) {
            $this->attributes["id"] = "_" . $this->slug;
            $this->attributes["name"] = $this->slug;
        }

        // Check required
        if ($this->required) {
            $this->attributes["required"] = true;
        }

        // Check required
        if ($this->required) {
            $this->attributes["required"] = true;
        }

        // Check readonly
        if ($this->readonly) {
            $this->attributes["readonly"] = true;
        }

        // Check min
        if (isset($this->min)) {
            $this->attributes["min"] = $this->min;
        }

        // Check max
        if (isset($this->max)) {
            $this->attributes["max"] = $this->max;
        }

        // Check minLength
        if (isset($this->minLength)) {
            $this->attributes["minlength"] = $this->minlength;
        }

        // Check maxLength
        if (isset($this->maxLength)) {
            $this->attributes["maxLength"] = $this->maxLength;
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

        // Output label
        if (!empty($this->label) && $this->type !== "hidden") {
            echo sprintf(
                "<label for=\"%s\">%s</label>",
                $this->slug,
                $this->label
            );

            // Output line-break
            if (!$this->inlineLabel) {
                echo "<br>";
            }

        }

        // Output
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
						if ($value === $this->value) {
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

        // Output linebreak
        if ($this->type !== "hidden" && !$this->inlineField) {
            echo "<br>";
        }
    }

    /**
     * @brief Output the input's script to the current output buffer
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
     * @brief Check if the input has value in a request
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
     * @brief Get the input field's value from a request
     * @return mixed Returns the value from the array, or `false` if not set
     */
    public function getValue() {
        // Check if the request has the value
        if ($this->hasValue()) {
            return Request::getPayload()[$this->slug];
        }
        else {
            return false;
        }
    }

    /**
     * @brief Check if the input is valid
     * @return boolean Returns if the input field is valid
     */
    public function isValid() {
        // Check if it's a button
        if ($this->type === "submit" || $this->type === "button") {
            return true;
        }

        // If not required and not filled out, it is valid
        if (!$this->required && empty($this->getValue())) {
            return "This field is required.";
        }

        // At this point, if it has no value, it is not valid
        if (!$this->hasValue()) {
            return "This field was not found.";
        }

        // Pull the value and the length
        $value = $this->getValue();

        // String checks
        if (is_string($value)) {
            // Find string length
            $length = strlen($value);
    
            // Check minLength
            if (isset($this->minLength) && $length < $this->minLength) {
                return
                    "This field must be at least " .
                    $this->minLength .
                    " characters long."
                ;
            }

            // Check maxLength
            if (isset($this->maxLength) && $length > $this->maxLength) {
                return
                    "This field must not be longer than " .
                    $this->maxLength .
                    " characters."
                ;
            }
        }

        // Numerical checks
        if (is_numeric($value)) {
            $value = intval($value);

            // Check minLength
            if ($value < $this->min) {
                return "This value must be at least " . $this->min . ".";
            }

            // Check maxLength
            if ($value > $this->max) {
                return "This value must not be larger than " . $this->max . ".";
            }
        }

        // All checks passed
        return true;

    }
}