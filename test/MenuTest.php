<?php

use Stateless\Menu;
use Stateless\MenuItem;
use Stateless\MenuIcon;
use PHPUnit\Framework\TestCase;

/**
 * @covers Menu, MenuItem, MenuIcon
 */
final class MenuTest extends TestCase {

    public function testCreateMenu() {
        $menu = new Menu(
            [
                new MenuItem("Test", "/"),
                new MenuIcon("fa-user", "/user", "Users", "top", ["class" => "icon"])
            ],
            [
                "class" => "test-menu"
            ]
        );

        $this->assertTrue(!empty($menu));
    }

    public function testShow() {
        $menu = new Menu(
            [
                new MenuItem("Test", "/"),
                new MenuIcon("fa-user", "/user", "Users", "top", ["class" => "icon"])
            ],
            [
                "class" => "test-menu"
            ]
        );

        ob_start();
        $menu->show();
        $result = ob_get_contents();

        ob_end_clean();

        $this->assertTrue(
            !empty($result) &&
            strlen($result) > 1
        );
    }

};
