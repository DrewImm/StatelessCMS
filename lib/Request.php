<?php

namespace Stateless;

/**
 * @brief Request is a class that holds data about the current http request
 */
class Request {
    protected static $path; /**< string Url path */
    protected static $dirs; /**< array Array of directories in path, including the ending file */
    protected static $domain; /**< string The full domain */
    protected static $domains; /**< array The domains as an array */
    protected static $subdomain; /**< string The first subdomain that is not www. */
    protected static $method; /**< string The request method ("GET", "POST", "DELETE", etc) */
    protected static $payload; /**< array Payload sent with the request */
    protected static $headers; /**< array Array of headers sent with the request */
    protected static $token; /**< string The bearer token pulled from the Authorization header */

    /**
     * @brief Get path from the url
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

            // Remove trailing slash
            Request::$path = preg_replace("{/$}", "", Request::$path);
        }

        return Request::$path;
    }
    
    /**
     * @brief Get the path directories
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
     * @brief Get the domain
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
     * @brief Get subdomains and domains
     * @return array Returns the domains as an array
     */
    public static function getDomains() {
        if (!isset(Request::$domains)) {
            Request::$domains = explode(".", Request::getDomain());
        }

        return Request::$domains;
    }

    /**
     * @brief Get the first subdomain that isnt "www"
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
     * @brief Get the request method
     * @return string Returns the request method
     */
    public static function getMethod() {
        if (!isset(Request::$method)) {
            // Pull method
            Request::$method = filter_input(
                INPUT_SERVER,
                "SERVER_METHOD",
                FILTER_SANITIZE_STRING
            );
    
            Request::$method = strtoupper(Request::$method);
        }
        
        return Request::$method;
    }

    /**
     * @brief Get the payload
     * @return array Returns the payload as an array
     */
    public static function getPayload() {
        if (!isset(Request::$payload)) {
            // Pull payload
            if (Request::getMethod() !== "GET") {
                Request::$payload = file_get_contents("php://input");
                Request::$payload = (array)json_decode(Request::$payload);
            }
            else if (!empty($_GET)) {
                Request::$payload = $_GET;
            }
        }

        return Request::$payload;
    }

    /**
     * @brief Get the headers
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
     * @brief Get the bearer token from the authentication header
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
}