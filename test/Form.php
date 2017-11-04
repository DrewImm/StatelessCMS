<?php

namespace Stateless;

class Form {
    public $name;
    public $method;
    public $inputs;
    public $action;
    public $uuid;
    public $obid;
    public $ttl;
    public $salt;
    public $pepperLength;
    public $nonceKey;

    /**
     * @brief Construct a new Form
     * @param string $name Form name to validate submission
     * @param array $inputs Array of FormInput objects to populate the form
     * @param string $method HTTP method to use.  Default is POST
     * @param string $action Form action.  Default is empty
     * @param integer $uuid User ID to validate submission.  Default is 0
     * @param integer $obid Object ID to validate submission.  Default is 0
     * @param integer $ttl Time (in seconds) for a form submission to live.
     *  Default is 3600 seconds.
     * @param string $salt Salt string to apply to the nonce.  Default is "_"
     * @param integer $pepperLength Length of the pepper to apply to the nonce.
     *  Default is 2 characters
     * @param string $nonceKey The key for the hidden nonce field.  Default is
     *  "__nonce"
     */
    public function __construct(
        $name,
        $input,
        $method = "POST",
        $action = "",
        $uuid = 0,
        $obid = 0,
        $ttl = 3600,
        $salt = "_",
        $pepperLength = 2,
        $nonceKey = "__nonce"
    ) {
        $this->name = $name;
        $this->method = $method;
        $this->inputs = $inputs;
        $this->action = $action;
        $this->uuid = $uuid;
        $this->obid = $obid;
        $this->ttl = $ttl;
        $this->salt = $salt;
        $this->pepperLength = $pepperLength;
        $this->nonceKey = $nonceKey;
    }

    /**
     * @brief Check for a submission
     * @param Request $request Request object to check for submission
     * @return boolean Returns if a submission exists in the request
     */
    public static function isSubmit($request) {
        // Checck for the nonce key in the request payload
        return (
            isset($request->payload) &&
            array_key_exists($this->nonceKey, $request->payload)
        );
    }

    /**
     * @brief Check if the form's nonce and data is valid
     * @param Request $request Request object to check submission from
     * @return boolean Returns if the form submission is valid
     */
    public static function isValid($request) {
        // Check for form submission
        if ($this->isSubmit($request)) {
            // Validate the nonce
            $valid = Crypto::validateNonce(
                $request->payload[$this->nonceKey],
                $this->name,
                $this->uuid,
                $this->obid,
                $this->ttl,
                $this->salt,
                $this->pepperLength
            );

            // Return if not valid
            if (!$valid) {
                return false;
            }

            // Check each input for validity
            foreach ($this->inputs as $input) {
                if (!$input->isValid($request)) {
                    return false;
                }
            }

            // Checks passed
            return true;
        }
        else {
            // No submission, not valid
            return false;
        }
    }

    /**
     * @brief Get an array of the fields' input values
     * @param Request $request Request object to get form submission from
     * @return mixed Returns key/value pairs of the form, or false on failure
     */
    public static function getValues($request) {
        $values = array();

        // Get each input value
        foreach ($this->inputs as $input) {
            $values[$input->slug] = $input->getValue($request);
        }

        // Return the values, or false if empty
        return empty($values) ? false : $values;
    }

    /**
     * @brief Output the form markup to the current output buffer
     */
    public static function getMarkup($request) {
        // Output form tag
        echo 
            "<form method=\"" . $this->method . "\"" .
            "action=\"" . $this->action . "\" " .
            "name=\"" . $this->name . "\">"
        ;

        // Create a nonce
        $nonce = Crypto::nonce(
            $this->name,
            $this->uuid,
            $this->obid,
            $this->ttl,
            $this->salt,
            $this->pepperLength
        );

        // Output nonce field
        Crypto::showNonceField(
            $this->nonceKey,
            $nonce
        );

        // Generate fields
        foreach ($this->inputs as $input) {
            echo $input->getMarkup($request);
        }
        
        // Closeup form
        echo "</form>";
    }
}