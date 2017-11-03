<?php

namespace Stateless;

/**
 * @brief A session tracks the php session
 */
class Session {
    public $id;
    public $uuid;
    public $nonce;
    public $userAgent;
    public $address;
    public $expires;
    public $name = "session";

    /**
     * @brief Construct a new session object
     */
    public function __construct() {
        session_start();
    }

    /**
     * @brief Create a new session object
     * @param integer $uuid User ID to associate the nonce with.  Default is 0
     * @param integer $ttl Time to live (days).  Default is 7
     * @param string $salt String to salt the nonce with.  Default is "$"
     * @param integer $pepperLength Length of the nonce pepper
     * @param string $prefix Session prefix.  Default is "__"
     */
    public static function create(
        $uuid = 0,
        $ttl = 7,
        $salt = "$",
        $pepperLength = 2,
        $prefix = "__"
    ) {
        // Destroy old session
        $this->destroy();

        // Use the user's address as the object Id
        $this->address = filter_input(
            INPUT_SERVER,
            "REMOTE_ADDR",
            FILTER_SANITIZE_URL
        );
        
        // Format $ttl to days
        $ttl *= 86400;

        // Add time to $ttl to get expiration
        $expires = $ttl + Crypto::nonceTime();

        // Create the nonce
        $this->nonce = Crypto::nonce(
            $this->name,
            $uuid,
            $this->address,
            $expires,
            $salt,
            $pepperLength
        );

        // Create the session object
        $this->uuid = $uuid;
        $this->expires = $expires;
        $this->userAgent = filter_input(
            INPUT_SERVER,
            "HTTP_USER_AGENT",
            FILTER_SANITIZE_STRING
        );

        // Create the session
        $_SESSION[$prefix . "n"] = $this->nonce;
        $_SESSION[$prefix . "u"] = $uuid;
        $_SESSION[$prefix . "a"] = $this->userAgent;
        $_SESSION[$prefix . "i"] = $this->address;
    }

    /**
     * @brief Destroy the session
     */
    public static function destroy() {
        session_destroy();
    }

    /**
     * @brief Check if the session is active (but maybe not valid)
     * @param string $prefix The session prefix to check for
     * @return boolean Returns if the session is active (but maybe not valid)
     */
    public static function isActive($prefix) {
        return (
            isset($_SESSION) &&
            !empty($_SESSION[$prefix . "n"])
        );
    }

    /**
     * @brief Check if the session is active and valid
     * @param string $prefix The session prefix to check for
     * @return boolean Returns if the session is valid
     */
    public static function isValid(
        $uuid = 0,
        $ttl = 7,
        $salt = "$",
        $pepperLength = 2,
        $prefix = "__"
    ) {
        // Check if the session is active
        if ($this->isActive($prefix)) {
            // Get address
            $this->address = filter_input(
                INPUT_SERVER,
                "REMOTE_ADDR",
                FILTER_SANITIZE_URL
            );

            // Format $ttl to days
            $ttl *= 86400;

            $this->uuid = $uuid;
            $this->ttl = $ttl;
            $this->salt = $salt;
            $this->pepperLength = $pepperLength;
            $this->prefix = $prefix;

            // Validate nonce
            $validateNonce = Crypto::validateNonce(
                $_SESSION[$prefix . "a"],
                $this->name,
                $this->uuid,
                $this->address,
                $this->ttl,
                $this->salt,
                $this->pepperLength
            );

            // Get stored user agent and address
            $this->agent = $_SESSION[$prefix . "a"];
            $this->address = $_SESSION[$prefix . "i"];

            // Get current user agent and address
            $agent = filter_input(
                INPUT_SERVER,
                "HTTP_USER_AGENT",
                FILTER_SANITIZE_STRING
            );
            $address = filter_input(
                INPUT_SERVER,
                "REMOTE_ADDR",
                FILTER_SANITIZE_URL
            );

            // Check user agent and address
            $validateAgent = (
                $agent === $this->agent &&
                $address === $this->address
            );

            // Check expiration
            // Todo

            // Return the results
            return ($validateNonce && $validateAgent);
        }
        
        // Session isn't even active
        return false;
    }

    /**
     * @brief Get the user ID
     * @param $prefix Session prefix to fetch the session $uuid from
     * @return mixed Returns the user ID or false on failure
     */
    public static function getUserId($prefix) {
        if (
            $this->isActive($prefix) &&
            !empty($_SESSION[$prefix . "u"])
        ) {
            return $_SESSION[$prefix . "u"];
        }

        // User Id not found
        return false;
    }
}