<?php

namespace Stateless;

/**
 * Request is a class that holds data about the current http request
 */
class Request {
    /** Url path */
    protected static $path;

    /** Array of directories in path, including the ending file */
    protected static $dirs;

    /** The full domain */
    protected static $domain;

    /** The domains as an array */
    protected static $domains;

    /** The first subdomain that is not www. */
    protected static $subdomain;

    /** The request method ("GET", "POST", "DELETE", etc) */
    protected static $method;

    /** array Payload sent with the request */
    protected static $payload;

    /** array Array of headers sent with the request */
    protected static $headers;

    /** string The bearer token pulled from the Authorization header */
    protected static $token;

    /**
     * Get path from the url
     * 
     * @return string Returns the url path
     */
    public static function getPath() {
        if (!isset(Request::$path)) {
            Request::$path = filter_input(
                INPUT_SERVER,
                "REQUEST_URI",
                FILTER_SANITIZE_URL
            );

            // Remove query string from path
            $i = strpos(Request::$path, "?");
            if ($i !== false) {
                Request::$path = substr(Request::$path, 0, $i);
            }
            
            // Remove trailing slashes
            Request::$path = rtrim(Request::$path, '/');

            // Add a single trailing slash if the path is empty
            if (Request::$path === '') {
                Request::$path = Request::$path . '/';
            }
        }

        return Request::$path;
    }
    
    /**
     * Get the path directories
     * 
     * @return array Returns an array of the path directories
     */
    public static function getDirs() {
        if (!isset(Request::$dirs)) {
            // Explode path
            Request::$dirs = explode("/", Request::getPath());
            
            // Remove empty
            $nDirs = count(Request::$dirs);
            for ($i = 0; $i < $nDirs; $i++) {
                if ($i === 0 && empty(Request::$dirs[$i])) {
                    unset(Request::$dirs[$i]);
                }
            }
    
            Request::$dirs = array_values(Request::$dirs);
        }

        return Request::$dirs;
    }

    /**
     * Get the domain
     * 
     * @return string Returns the domain as a string
     */
    public static function getDomain() {
        if (!isset(Request::$domain)) {
            // Pull domains
            Request::$domain = filter_input(
                INPUT_SERVER,
                "SERVER_NAME",
                FILTER_SANITIZE_URL
            );
        }

        return Request::$domain;
    }

    /**
     * Get subdomains and domains
     * 
     * @return array Returns the domains as an array
     */
    public static function getDomains() {
        if (!isset(Request::$domains)) {
            Request::$domains = explode(".", Request::getDomain());
        }

        return Request::$domains;
    }

    /**
     * Get the first subdomain that isnt "www"
     * 
     * @return string Returns the subdomain, or the main domain if none exist
     */
    public static function getSubdomain() {
        if (!isset(Request::$subdomain)) {
            $domains = Request::getDomains();
            Request::$subdomain = reset($domains);
            
            while (Request::$subdomain === "www") {
                Request::$subdomain = next($domains);
            }
        }

        return Request::$subdomain;
    }

    /**
     * Get the request method
     * 
     * @return string Returns the request method
     */
    public static function getMethod() {
        if (!isset(Request::$method)) {
            // Pull method
            Request::$method = filter_input(
                INPUT_SERVER,
                "REQUEST_METHOD",
                FILTER_SANITIZE_STRING
            );
    
            Request::$method = strtoupper(Request::$method);
        }
        
        return Request::$method;
    }

    /**
     * Get the payload
     * 
     * @return array Returns the payload as an array
     */
    public static function getPayload() {
        if (!isset(Request::$payload)) {
            // Pull payload
            if (Request::getMethod() !== "GET") {
                $payload = file_get_contents("php://input");
                $headers = Request::getHeaders();
                $ctype = false;
                
                // Check the content type
                if ($headers && array_key_exists("Content-Type", $headers)) {
                    $ctype = $headers["Content-Type"];
                }

                // JSON
                if ($ctype == "application/json" || $ctype == "application/javascript") {
                    $json = (array)json_decode($payload);
                    
                    if (json_last_error() == JSON_ERROR_NONE) {
                        $payload = $json;
                    }
                }
                // POST data
                else if (Request::getMethod() === "POST") {
                    $payload = $_POST;
                }

                Request::$payload = $payload;
            }
            else if (!empty($_GET)) {
                Request::$payload = $_GET;
            }
        }

        return Request::$payload;
    }

    /**
     * Get the headers
     * 
     * @return mixed Returns the headers as an array, or false if cannot access
     */
    public static function getHeaders() {
        if (!isset(Request::$headers)) {
            // Pull headers
            if (function_exists("apache_request_headers")) {
                Request::$headers = apache_request_headers();
            }
        }

        return Request::$headers;
    }

    /**
     * Get the bearer token from the authentication header
     * 
     * @return mixed Returns the token as a string, or false if cannot access
     */
    public static function getToken() {
        if (!isset(Request::$token)) {
            // Pull headers
            $headers = Request::getHeaders();

            // Pull bearer
            if (!empty($headers) && isset($headers["Authorization"])) {
                $header = $headers["Authorization"];
                Request::$token = null;
    
                if (!empty($header)) {
                    try {
                        list(Request::$token) = sscanf($header, "Bearer %s");
                    }
                    catch (\Exception $e) {
                        Request::$token = null;
                    }
                }
            }
        }

        return Request::$token;
    }

};
