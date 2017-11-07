<?php

namespace Stateless;

/**
 * @brief A Session tracks the php session
 */
class Session {
    private static $id; /**< Session id */
    private static $uuid; /**< Session user Id */
    private static $nonce; /**< Session nonce */
    private static $userAgent; /**< Session user agent */
    private static $address; /**< Session client IP address */
    private static $expires; /**< Time since epoch the session expires */
    private static $name = "session"; /**< Session name, for verification.  Default is "session" */
    private static $prefix; /**< Session prefix */

    /**
     * @brief Create a new session
     * @param string $cipherKey Cipher key to encrypt the nonce field
     * @param integer $uuid User ID to associate the nonce with.  Default is 0
     * @param integer $ttl Time to live (days).  Default is 7
     * @param string $salt String to salt the nonce with.
     * @param integer $pepperLength Length of the nonce pepper
     * @param string $prefix Session prefix.  Default is "__"
     */
    public static function create(
        $cipherKey,
        $uuid = 0,
        $ttl = 7,
        $salt = "_",
        $pepperLength = 3,
        $prefix = "__"
    ) {
        // Destroy old session
        Session::destroy();

        // Use the user's address as the object Id
        Session::$address = filter_input(
            INPUT_SERVER,
            "REMOTE_ADDR",
            FILTER_SANITIZE_URL
        );
        
        // Format $ttl to days
        $ttl *= 86400;

        // Add time to $ttl to get expiration
        $expires = $ttl + Crypto::nonceTime();

        // Create the nonce
        Session::$nonce = Crypto::nonce(
            Session::$name,
            $uuid,
            Session::$address,
            $expires,
            $salt,
            $pepperLength,
            $cipherKey
        );

        // Create the session object
        Session::$uuid = $uuid;
        Session::$expires = $expires;
        Session::$userAgent = filter_input(
            INPUT_SERVER,
            "HTTP_USER_AGENT",
            FILTER_SANITIZE_STRING
        );

        // Create the session
        $_SESSION[$prefix . "n"] = Session::$nonce;
        $_SESSION[$prefix . "u"] = $uuid;
        $_SESSION[$prefix . "a"] = Session::$userAgent;
        $_SESSION[$prefix . "i"] = Session::$address;
    }

    /**
     * @brief Destroy the session
     */
    public static function destroy() {
        if (!empty($_SESSION)) {
            session_destroy();
            unset($_SESSION);
        }
    }

    /**
     * @brief Check if the session is active (but maybe not valid)
     * @param string $prefix The session prefix to check for
     * @return boolean Returns if the session is active (but maybe not valid)
     */
    public static function isActive($prefix = "__") {
        return (
            isset($_SESSION) &&
            !empty($_SESSION[$prefix . "n"])
        );
    }

    /**
     * @brief Check if the session is active and valid
     * @param string $cipherKey Cipher key to encrypt the nonce field
     * @param integer $uuid User id to validate.  Default is 0
     * @param integer $ttl Time to live of the session (days).  Default is 7
     * @param string $salt Salt to append to the nonce.  Default is "$"
     * @param integer $pepperLength Length of pepper for nonce.  Default is 2
     * @param string $prefix The session prefix to check for.  Default is "__"
     * @return boolean Returns if the session is valid
     */
    public static function isValid(
        $cipherKey,
        $uuid = 0,
        $ttl = 7,
        $salt = "$",
        $pepperLength = 2,
        $prefix = "__"
    ) {
        // Check if the session is active
        if (Session::isActive($prefix)) {
            // Get address
            $address = filter_input(
                INPUT_SERVER,
                "REMOTE_ADDR",
                FILTER_SANITIZE_URL
            );

            // Format $ttl to days
            $ttl *= 86400;

            Session::$uuid = $uuid;
            Session::$prefix = $prefix;

            // Validate nonce
            $validateNonce = Crypto::validateNonce(
                $_SESSION[$prefix . "n"],
                Session::$name,
                $uuid,
                $address,
                $ttl,
                $salt,
                $pepperLength,
                $cipherKey
            );

            // Get stored user agent and address
            $sAgent = $_SESSION[$prefix . "a"];
            $sAddress = $_SESSION[$prefix . "i"];

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
                $agent === $sAgent &&
                $address === $sAddress
            );
            
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
    public static function getUserId($prefix = "__") {
        if (
            Session::isActive($prefix) &&
            !empty($_SESSION[$prefix . "u"])
        ) {
            return $_SESSION[$prefix . "u"];
        }

        // User Id not found
        return false;
    }
}