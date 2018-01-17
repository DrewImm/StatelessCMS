<?php

use Stateless\Request;
use PHPUnit\Framework\TestCase;

/**
 * @covers Request
 */
final class RequestTest extends TestCase {

    public function testGetPath() {
        // TODO - Can this be tested?
        /*
        $this->assertTrue(empty(Request::getPath()));
        */
    }

    public function testGetDirs() {
        // TODO - Can this be tested?
        /*
        $this->assertTrue(
            empty(Request::getDirs())
        );
        */
    }

    public function testGetDomain() {
        $this->assertTrue(
            empty(Request::getDomain())
        );
    }

    public function testGetDomains() {
        $test = Request::getDomains();


        $this->assertTrue(
            is_array(Request::getDomains())
        );
    }

    public function testGetSubdomain() {
        $this->assertTrue(
            empty(Request::getSubdomain())
        );
    }

    public function testGetMethod() {
        $this->assertTrue(
            empty(Request::getMethod())
        );
    }

    public function testGetPayload() {
        $this->assertTrue(
            empty(Request::getPayload())
        );
    }

    public function testGetHeaders() {
        $this->assertTrue(
            empty(Request::getHeaders())
        );
    }

    public function testGetToken() {
        $this->assertTrue(
            empty(Request::getToken())
        );
    }

};
