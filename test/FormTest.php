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
        global $form;
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
        global $form;

        $this->assertFalse($form->isSubmit());
    }

    public function testShow() {
        global $form;
        global $iv;
        global $tag;

        if ($iv = Crypto::getIv()) {
            ob_start();
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
        global $form;
        global $iv;
        global $tag;

        $this->assertFalse($form->isValid($iv, $tag));
    }

    public function testGetValues() {
        global $form;
        $values = $form->getValues();

        $this->assertFalse($values);
    }
}