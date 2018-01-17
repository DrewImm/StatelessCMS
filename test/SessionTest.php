<?php

use Stateless\Session;
use Stateless\Crypto;
use PHPUnit\Framework\TestCase;

const prefix = "__";

defined("CIPHER_KEY") or
    define("CIPHER_KEY", "lKKL2UuzmHKdBB1vHDFzNzCiTei6jWOTDdE7tJPPMGYQpnohGQUhVduwZTSP1ESyUcLMUPhPhCCEuSYGIJr0gw==");

if (!defined("NONCE_TIME_LENGTH")) define("NONCE_TIME_LENGTH", 10);
/**
 * @covers Session
 */
final class SessionTest extends TestCase {

    public function testCreate() {
        Session::create(
            CIPHER_KEY,
            uuid,
            ttl,
            salt,
            pepperLen,
            prefix
        );

        $this->assertTrue(Session::isActive(prefix));
    }

    public function testIsValid() {
        Session::create(
            CIPHER_KEY,
            uuid,
            ttl,
            salt,
            pepperLen,
            prefix
        );

        $this->assertTrue(
            Session::isValid(
                CIPHER_KEY,
                uuid,
                ttl,
                salt,
                pepperLen,
                prefix
            )
        );
    }

    public function testGetUserId() {
        Session::create(
            CIPHER_KEY,
            uuid,
            ttl,
            salt,
            pepperLen,
            prefix
        );

        $this->assertTrue(
            Session::getUserId(prefix) >= 0
        );
    }

    public function testDestroy() {
        Session::create(
            CIPHER_KEY,
            uuid,
            ttl,
            salt,
            pepperLen,
            prefix
        );
        
        // TODO - Can this be tested?
        /*
        Session::destroy();
    
        $this->assertFalse(Session::isActive(prefix));
        */
    }

};
