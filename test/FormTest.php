<?php

use Stateless\Form;
use Stateless\FormInput;
use Stateless\Crypto;
use Stateless\Request;
use PHPUnit\Framework\TestCase;

defined("CIPHER_KEY") or
define("CIPHER_KEY", "lKKL2UuzmHKdBB1vHDFzNzCiTei6jWOTDdE7tJPPMGYQpnohGQUhVduwZTSP1ESyUcLMUPhPhCCEuSYGIJr0gw==");

/**
 * @covers Form, FormInput
 */
final class FormTest extends TestCase {
    public function testConstruct() {
        $form = new Form(
            "test-form",
            [
                new FormInput("Name", "name"),
                new FormInput("Email", "email")
            ],
            CIPHER_KEY
        );

        $this->assertNotFalse($form);
    }

    public function testIsSubmit() {
        $form = new Form(
            "test-form",
            [
                new FormInput("Name", "name"),
                new FormInput("Email", "email")
            ],
            CIPHER_KEY
        );

        $this->assertFalse($form->isSubmit());
    }

    public function testShow() {
        $form = new Form(
            "test-form",
            [
                new FormInput("Name", "name"),
                new FormInput("Email", "email")
            ],
            CIPHER_KEY
        );

        if ($iv = Crypto::getIv()) {
            ob_start();
            $tag = null;

            $form->show($iv, $tag);
            $markup = ob_get_contents();
            
            ob_end_clean();

            $this->assertTrue(
                !empty($markup) &&
                strlen($markup) > 1
            );
        }
    }

    public function testIsValid() {
        $form = new Form(
            "test-form",
            [
                new FormInput("Name", "name"),
                new FormInput("Email", "email")
            ],
            CIPHER_KEY
        );

        if ($iv = Crypto::getIv()) {
            ob_start();
            $tag = null;
            $form->show($iv, $tag);
            $markup = ob_get_contents();
            
            ob_end_clean();

            $this->assertTrue(
                !empty($markup) &&
                strlen($markup) > 1
            );
        }

        $this->assertFalse($form->isValid($iv, $tag));
    }

    public function testGetValues() {
        $form = new Form(
            "test-form",
            [
                new FormInput("Name", "name"),
                new FormInput("Email", "email")
            ],
            CIPHER_KEY
        );

        $values = $form->getValues();

        $this->assertFalse($values);
    }
}