<?php

use Stateless\Crypto;
use PHPUnit\Framework\TestCase;

defined("CIPHER_KEY") or
    define("CIPHER_KEY", "lKKL2UuzmHKdBB1vHDFzNzCiTei6jWOTDdE7tJPPMGYQpnohGQUhVduwZTSP1ESyUcLMUPhPhCCEuSYGIJr0gw==");

/**
 * @covers Crypto
 */
const in = "Test";
const uuid = 123;
const salt = "_salt";
const pepperLen = 3;
const ttl = 3600;

final class CryptoTest extends TestCase {

    public function testHash() {
        $hash = Crypto::hash(in);

        $this->assertTrue(password_verify(in, $hash));
    }

    public function testVerifyHash() {
        $hash = Crypto::hash(in);

        $this->assertTrue(Crypto::verifyHash(in, $hash));
    }

    public function testSalt() {
        $salted = in . salt;

        $this->assertEquals(Crypto::salt(in, salt), $salted);
    }

    public function testGetPepper() {
        $this->assertTrue(
            strlen(Crypto::getPepper(uuid, pepperLen)) === 3
        );
    }

    public function testPepper() {
        $this->assertTrue(
            strlen(Crypto::pepper(in, uuid, pepperLen)) >
            strlen(in)
        );
    }

    public function testSpice() {
        $this->assertTrue(
            strlen(Crypto::spice(in, uuid, salt, pepperLen)) > 
            strlen(in)
        );
    }

    public function testCheckSalt() {
        $salted = Crypto::salt(in, salt);

        $this->assertTrue(
            Crypto::checkSalt($salted, salt)
        );
    }

    public function testCheckPepper() {
        $pepper = Crypto::pepper(in, uuid, pepperLen);

        $this->assertTrue(
            Crypto::checkPepper($pepper, uuid, pepperLen)
        );
    }

    public function testCheckSpice() {
        $spice = Crypto::spice(in, uuid, salt, pepperLen);

        $this->assertTrue(
            Crypto::checkSpice($spice, uuid, salt, pepperLen)
        );
    }

    public function testUnsalt() {
        $salted = Crypto::salt(in, salt);

        $this->assertTrue(
            strlen($salted) >
            strlen(Crypto::unsalt($salted, salt))
        );
    }

    public function testUnpepper() {
        $pepper = Crypto::pepper(in, uuid, pepperLen);

        $this->assertTrue(
            strlen($pepper) >
            strlen(Crypto::unpepper($pepper, uuid, pepperLen))
        );
    }

    public function testUnspice() {
        $spice = Crypto::spice(in, uuid, salt, pepperLen);

        $this->assertTrue(
            strlen($spice) >
            strlen(Crypto::unspice($spice, uuid, salt, pepperLen))
        );
    }
    
    public function testGetKey() {
        $keylen = 64;
        $key = Crypto::getKey($keylen);
        $key = base64_decode($key);

        $this->assertTrue(
            strlen($key) === $keylen
        );
    }

    public function testGetIv() {
        $this->assertNotFalse(
            Crypto::getIv()
        );
    }

    public function testEncryptDecrypt() {
        $encrypted = Crypto::encrypt(
            in,
            CIPHER_KEY
        );

        $this->assertEquals(
            Crypto::decrypt(
                $encrypted,
                CIPHER_KEY
            ),
            in
        );
    }

    public function testNonceTime() {
        $this->assertEquals(
            time(),
            Crypto::nonceTime()
        );
    }

    public function testNonce() {
        $nonce = Crypto::nonce(
            "test-nonce",
            uuid,
            0,
            ttl,
            salt,
            pepperLen,
            CIPHER_KEY
        );
        $valid = Crypto::validateNonce(
            $nonce,
            "test-nonce",
            uuid,
            0,
            ttl,
            salt,
            pepperLen,
            CIPHER_KEY
        );

        $this->assertTrue($valid);
    }

    public function testGetNonceField() {
        $nonce = Crypto::nonce(
            "test-nonce",
            uuid,
            0,
            3600,
            salt,
            pepperLen,
            CIPHER_KEY
        );
        $field = Crypto::getNonceField(
            "__nonce",
            $nonce
        );

        $this->assertTrue(
            is_string($field) &&
            strlen($field) > 10
        );
    }

};
