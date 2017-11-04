<?php

namespace Stateless;

/**
 * Request is a class that holds data about the current http request
 */
class Request {
    public $path; /**< string Url path */
    public $dirs; /**< array Array of directories in path, including the ending file */
    public $subdomain; /**< string The first subdomain that is not www. */
    public $domain; /**< string The full domain */
    public $method; /**< string The request method ("GET", "POST", "DELETE", etc) */
    public $payload; /**< array Payload sent with the request */
    public $headers; /**< array Array of headers sent with the request */
    public $token; /**< string The bearer token pulled from the Authorization header */

    /**
     * @brief Construct a request object
     */
    public function __construct() {
        $this->pull();
    }

    /**
     * @brief Pull the current request
     */
    public static function pull() {
        // Pull path
        $this->path = filter_input(
            INPUT_SERVER,
            "REQUEST_URI",
            FILTER_SANITIZE_URL
        );

        // Remove query string from path
        $i = strpos($this->path, "?");
        if ($i !== false) {
            $this->path = substr($this->path, 0, $i);
        }

        // Remove trailing slash
        $this->path = preg_replace("{/$}", "", $this->path);

        // Explode path
        $this->dirs = explode("/", $this->path);
        
        $nDirs = count($this->dirs);
        for ($i = 0; $i < $nParts; $i++) {
            if ($i === 0 && empty($this->dirs[$i])) {
                unset($this->dirs[$i]);
            }
        }

        $this->dirs = array_values($this->dirs);

        // Pull domains
        $this->domain = filter_input(
            INPUT_SERVER,
            "SERVER_NAME",
            FILTER_SANITIZE_URL
        );

        $this->domains = explode(".", $this->domain);
        $this->subdomain = reset($this->domains);
        
        while ($this->subdomain === "www") {
            $this->subdomain = next($this->domains);
        }

        // Pull method
        $this->method = filter_input(
            INPUT_SERVER,
            "SERVER_METHOD",
            FILTER_SANITIZE_STRING
        );

        $this->method = strtoupper($this->method);

        // Pull payload
        if ($this->method !== "GET") {
            $this->payload = file_get_contents("php://input");
            $this->payload = (array)json_decode($this->payload);
        }
        else if (!empty($_GET)) {
            $this->payload = $_GET;
        }

        // Pull headers
        if (function_exists("apache_request_headers")) {
            $this->headers = apache_request_headers();
        }

        // Pull bearer
        if (!empty($this->headers) && isset($this->headers["Authorization"])) {
            $header = $this->headers["Authorization"];
            $this->token = null;

            if (!empty($header)) {
                try {
                    list($this->token) = sscanf($header, "Bearer %s");
                }
                catch (\Exception $e) {
                    $this->token = null;
                }
            }
        }
    }
}