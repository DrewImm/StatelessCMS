<?php

namespace Stateless;

use Stateless\Request;

/**
 * Create and verify html forms
 */
class Form {

    /** Form name to validate submission */
    public $name = "untitled-form";

    /** Array of FormInput objects to populate the form */
    public $inputs = [];

    /** HTTP method to use. */
    public $method = "post";

    /** Form action. */
    public $action = "";

    /** Array of attributes to inlcude with the form */
    public $attributes;
    
    /** Array of attributes to push to the children */
    public $childAttributes;

    /** User ID to validate submission. */
    public $uuid = 0;

    /** Object ID to validate submission. */
    public $obid = 0;

    /** Time (in seconds) for a form submission to live. */
    public $ttl = 3600;

    /** Salt string to apply to the nonce. */
    public $salt = "_";

    /** Length of the pepper to apply to the nonce. */
    public $pepperLength = 2;

    /** The key for the hidden nonce field. */
    public $nonceKey = "__nonce";

    /** Cipher key to encrypt the nonce field */
    public $cipherKey = CIPHER_KEY;

    /** Result of form submission */
    protected $result;

    /** Error message to be shown */
    protected $error;

    /**
     * Construct a new Form
     * 
     * @param array $data (Optional) Array of data for the form
     */
    public function __construct($data = []) {

        // Check if $data is array
        if ($data && is_array($data)) {

            // Name
            if (array_key_exists("name", $data)) {
                $this->name = $data["name"];
            }

            // Inputs
            if (array_key_exists("inputs", $data)) {
                $this->inputs = $data["inputs"];
            }

            // Method
            if (array_key_exists("method", $data)) {
                $this->method = $data["method"];
            }

            // Action
            if (array_key_exists("action", $data)) {
                $this->action = $data["action"];
            }

            // Attributes
            if (array_key_exists("attributes", $data)) {
                $this->attributes = $data["attributes"];
            }

            // Attributes
            if (array_key_exists("child_attributes", $data)) {
                $this->childAttributes = $data["child_attributes"];
            }

            // User ID
            if (array_key_exists("uuid", $data)) {
                $this->uuid = $data["uuid"];
            }

            // Object ID
            if (array_Key_exists("obid", $data)) {
                $this->obid = $data["obid"];
            }

            // Time to live
            if (array_key_exists("ttl", $data)) {
                $this->ttl = $data["ttl"];
            }

            // Salt
            if (array_key_exists("salt", $data)) {
                $this->salt = $data["salt"];
            }

            // Pepper length
            if (array_key_exists("pepper_length", $data)) {
                $this->pepperLength = $data["pepper_length"];
            }

            // Nonce key
            if (array_key_exists("nonce_key", $data)) {
                $this->nonceKey = $data["nonce_key"];
            }

            // Cipher key
            if (array_key_exists("cipher_key", $data)) {
                $this->cipherKey = $data["cipher_key"];
            }
        }
        
        // Check for init method
        $this->init();
    }

    /**
     * Get an array of the fields' input values
     * 
     * @return mixed Returns key/value pairs of the form, or false on failure
     */
    public function getValues() {

        // Create values
        $values = array();

        // Loop through inputs
        foreach ($this->inputs as $input) {
            $value = $input->getValue();

            // Get value, if it exists
            if ($value !== false) {
                $values[$input->slug] = $value;
            }
        }

        // Return the values, or false if empty
        return empty($values) ? false : $values;
    }

    /**
     * Initialize this Form
     */
    public function init() {

        // Check if we have child atttributes
        if (is_array($this->childAttributes)) {

            // Count number of children who need them
            $nChildren = count($this->inputs);

            // Loop through children
            for ($i = 0; $i < $nChildren; $i++) {

                // Check if the child already has attributes
                if (is_array($this->inputs[i]->attributes)) {

                    // Merge attributes
                    $this->inputs[$i]->attributes = array_merge(
                        $this->inputs[$i]->attributes, $this->childAttributes);
                }
                else {

                    // Insert new attributes
                    $this->inputs[$i]->attributes = $this->childAttributes;
                }
            }
        }

        // Check for submission
        $this->isSubmit();
        $this->isValid();

    }

    /**
     * Check for a submission
     * 
     * @return boolean Returns if a submission exists in the request
     */
    public function isSubmit() {

        // Get the Request payload
        $payload = Request::getPayload();

        // Check for the nonce key
        if (!empty($payload) &&
            array_key_exists($this->nonceKey, $payload)) {
            
            // Valid submission
            if (method_exists($this, "onSubmit")) {

                // Run onSubmit() callback
                $this->onSubmit();
                
            }

            // Set submitted to true
            $this->submitted = true;
        }
        else {

            // No submission
            $this->submitted = false;

        }

        // Return results
        return $this->submitted;

    }

    /**
     * Check if the form's nonce and data is valid
     * 
     * @return boolean Returns if the form submission is valid
     */
    public function isValid() {

        // Check for form submission
        if ($this->isSubmit()) {
            
            // Validate the nonce
            $valid = Crypto::validateNonce(
                Request::getPayload()[$this->nonceKey],
                $this->name,
                $this->uuid,
                $this->obid,
                $this->ttl,
                $this->salt,
                $this->pepperLength,
                $this->cipherKey
            );
            
            // Check if nonce is valid
            if ($valid) {

                // Loop through inputs
                foreach ($this->inputs as $input) {

                    // Check if input is valid
                    $valid = $input->isValid();

                    if ($valid !== true) {
                        // Invalid input
                        $this->valid = false;

                        // Run onInvalidInput() callback
                        $this->onInvalidInput($input->slug, $valid);

                        // Break out of loop
                        break;
                    }
                }
    
                // Check if inputs were valid
                if ($valid) {

                    // Run onValid() callback
                    if (method_exists($this, "onValid")) {
                        $this->onValid();
                    }
        
                    // Checks passed
                    $this->valid = true;
                }
            }
            else {
                // Invalid nonce
                $this->valid = false;

                // Run invalid form callback
                $this->onInvalidForm();
            }
        }

        return (isset($this->valid) && $this->valid === true);
    }

    /**
     * Handle invalid Form Input values
     * 
     * @param string $slug Form Input slug which is invalid
     * @param string $message Invalid exception message descriptor
     */
    public function onInvalidInput($slug, $message) {
        $this->result = false;
        $this->error = $message;
    }

    /**
     * Handle invalid Forms
     */
    public function onInvalidForm() {
        // TODO - Test
        $this->result = false;
        $this->error = "This form has expired.  Please try again.";
    }

    /**
     * Output the form markup to the current output buffer
     */
    public function show() {

        // Output form tag
        echo  "<form method=\"" . $this->method . "\"" .
            "action=\"" . $this->action . "\" " .
            "id=\"form-" . $this->name . "\" ";

        // Form attributes
        if (is_array($this->attributes)) {

            // Output key/value pairs
            foreach ($this->attributes as $key => $value) {
                echo $key . "=\"" . $value . "\" ";
            }

        }
        else if (is_string($this->attributes)) {

            // Output string
            echo $this->attributes;

        }

        // Close form tag
        echo "name=\"" . $this->name . "\">";

        // Create a nonce
        $nonce = Crypto::nonce(
            $this->name,
            $this->uuid,
            $this->obid,
            $this->ttl,
            $this->salt,
            $this->pepperLength,
            $this->cipherKey
        );

        // Output nonce field
        echo Crypto::getNonceField(
            $this->nonceKey,
            $nonce
        );

        // Generate fields
        foreach ($this->inputs as $input) {
            echo $input->show();
        }
        
        // Closeup form
        echo "</form>";

    }

};
