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
        global $iv;
        global $tag;

        if ($iv = Crypto::getIv()) {
            Session::create(
                CIPHER_KEY,
                $iv,
                $tag,
                uuid,
                ttl,
                salt,
                pepperLen,
                prefix
            );
    
            $this->assertTrue(Session::isActive(prefix));
        }
    }

    public function testIsValid() {
        global $iv;
        global $tag;

        $this->assertTrue(
            Session::isValid(
                CIPHER_KEY,
                $iv,
                $tag,
                uuid,
                ttl,
                salt,
                pepperLen,
                prefix
            )
        );
    }

    public function testGetUserId() {
        global $iv;
        global $tag;

        $this->assertTrue(
            Session::getUserId(prefix) >= 0
        );
    }

    public function testDestroy() {
        Session::destroy();
    
        $this->assertFalse(Session::isActive(prefix));
    }
}