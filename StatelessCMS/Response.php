<?php

namespace Stateless;

/**
 * Response is a singleton that holds data about the soon-to-be http response
 */
class Response {

    /**
     * Send a raw header by status code
     * 
     * @param mixed $header (int) HTTP Response code or (string) HTTP
     *  Response header
     * @param boolean $exit If the script should exit after sending the
     *  header.  Default is `false`
     * @param string $protocol HTTP Protocol version to use.  Default is "HTTP/1.1"
     * @return boolean Returns true or exits on success, or false if bad input
     */
    public static function header(
        $header,
        $exit = false,
        $protocol = "HTTP/1.1"
    ) {
        // Translate response codes to header strings
        if (is_int($header)) {
            switch($header) {
                case 100:
                    $header = "100 Continue";
                break;

                case 101:
                    $header = "101 Switching Protocols";
                break;

                case 102:
                    $header = "102 Processing";
                break;

                case 200:
                    $header = "200 OK";
                break;

                case 201:
                    $header = "201 Created";
                break;
                
                case 202:
                    $header = "202 Accepted";
                break;

                case 203:
                    $header = "203 Non-Authoritative Information";
                break;

                case 204:
                    $header = "204 No Content";
                break;

                case 205:
                    $header = "205 Reset Content";
                break;

                case 206:
                    $header = "206 Partial Content";
                break;

                case 207:
                    $header = "207 Multi-Status";
                break;

                case 208:
                    $header = "208 Already Reported";
                break;

                case 226:
                    $header = "226 IM Used";
                break;

                case 300:
                    $header = "300 Multiple Choices";
                break;

                case 301:
                    $header = "301 Moved Permanently";
                break;

                case 302:
                    $header = "302 Found";
                break;

                case 303:
                    $header = "303 See Other";
                break;

                case 304:
                    $header = "304 Not Modified";
                break;

                case 305:
                    $header = "305 Use Proxy";
                break;

                case 306:
                    $header = "306 Switch Proxy";
                break;

                case 307:
                    $header = "307 Temporary Redirect";
                break;

                case 308:
                    $header = "308 Permanent Redirect";
                break;

                case 400:
                    $header = "400 Bad Request";
                break;

                case 401:
                    $header = "401 Unauthorized";
                break;

                case 402:
                    $header = "402 Payment Required";
                break;

                case 403:
                    $header = "403 Forbidden";
                break;

                case 404:
                    $header = "404 Not Found";
                break;

                case 405:
                    $header = "405 Method Not Allowed";
                break;

                case 406:
                    $header = "406 Not Acceptable";
                break;

                case 407:
                    $header = "407 Proxy Authentication Required";
                break;

                case 408:
                    $header = "408 Request Timeout";
                break;

                case 409:
                    $header = "409 Conflict";
                break;

                case 410:
                    $header = "410 Gone";
                break;

                case 411:
                    $header = "411 Length Required";
                break;

                case 412:
                    $header = "412 Precondition Failed";
                break;

                case 413:
                    $header = "413 Payload Too Large";
                break;

                case 414:
                    $header = "414 URI Too Long";
                break;

                case 415:
                    $header = "415 Unsupported Media Type";
                break;

                case 416:
                    $header = "416 Range Not Satisfiable";
                break;

                case 417:
                    $header = "417 Expectation Failed";
                break;

                case 418:
                    $header = "418 I'm a teapot";
                break;

                case 421:
                    $header = "421 Misdirected Request";
                break;

                case 422:
                    $header = "422 Unprocessable Entity";
                break;

                case 423:
                    $header = "423 Locked";
                break;

                case 424:
                    $header = "424 Failed Dependency";
                break;

                case 426:
                    $header = "426 Upgrade Required";
                break;

                case 428:
                    $header = "428 Precondition Required";
                break;

                case 429:
                    $header = "429 Too Many Requests";
                break;

                case 431:
                    $header = "431 Request Header Fields Too Large";
                break;

                case 451:
                    $header = "451 Unavailable For Legal Reasons";
                break;

                case 500:
                    $header = "500 Internal Server Error";
                break;

                case 501:
                    $header = "501 Not Implemented";
                break;

                case 502:
                    $header = "502 Bad Gateway";
                break;

                case 503:
                    $header = "503 Service Unavailable";
                break;

                case 504:
                    $header = "504 Gateway Timeout";
                break;

                case 505:
                    $header = "505 HTTP Version Not Supported";
                break;

                case 506:
                    $header = "506 Variant Also Negotiates";
                break;

                case 507:
                    $header = "507 Insufficient Storage";
                break;

                case 508:
                    $header = "508 Loop Detected";
                break;

                case 510:
                    $header = "510 Not Extended";
                break;

                case 511:
                    $header = "511 Network Authentication Required";
                break;

                default:
                    throw new \Exception ("Response::header does not exist.");
                break;
            }

            $header = $protocol . " " . $header;
        }

        if (is_string($header)) {
            // Send the header
            header ($header);

            // Exit if the $exit paramater is true
            if ($exit) {
                exit;
            }

            return true;
        }
        else {
            return false;
        }
    }

    /**
     * Redirect the browser to $location
     * 
     * @param string $location URL to redirect to
     */
    public static function redirect($location) {
        header ("Location: " . $location);
        exit;
    }
    
    /**
     * Refresh the current page
     */
    public static function refresh() {
        header("Refresh: 0");
    }

    /**
     * Send a Javascript Web Token (JWT) as a javascript variable in the
     *  response body.  Should only be used if utilizing https and the client
     *  is authenticated to prevent broadcasting the jwt as clear-text.
     * 
     * @param string $token JWT to send
     * @param string $key Javascript variable name.  Default is "AUTH_KEY"
     */
    public static function sendToken($token, $key = "AUTH_KEY") {
        echo "<script>var " . $key . " = \"" . $token . "\";</script>";
    }

};
