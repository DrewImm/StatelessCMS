<?php

use Stateless\Database;
use Stateless\DatabaseColumn;
use PHPUnit\Framework\TestCase;

/**
 * @covers Database, DatabaseColumn
 */
final class DatabaseTest extends TestCase {
    public function testConnect() {
        global $db;

        $db = new Database(
            "localhost",
            "stateless",
            "testpass1",
            "stateless",
            "s_"
        );

        $this->assertNotFalse($db);
    }

    public function testIsActive() {
        global $db;

        $this->assertTrue($db->isActive());
    }

    public function testError() {
        // Todo
        global $db;

        $this->assertNotFalse($db->error());
    }

    public function testQuery() {
        global $db;
        $result = $db->query("CREATE TABLE IF NOT EXISTS `s_test_make_table` (id INT NOT NULL);");

        $this->assertNotFalse($result);
    }

    public function testPreparedQuery() {
        global $db;
        $result = $db->preparedQuery(
            "INSERT INTO `s_test_make_table` (id) VALUES (?);",
            [
                10
            ]
        );

        $this->assertNotFalse($result);
    }

    public function testCreateTable() {
        global $db;
        $result = $db->createTable("test_create_table",
        [
            new DatabaseColumn("id", "int", true),
            new DatabaseColumn("name", "varchar(255)")
        ]);

        $this->assertNotFalse($result);
    }

    public function testNRows() {
        global $db;
        $result = $db->nRows("test_make_table");

        $this->assertNotFalse($db);
    }

    public function testSelect() {
        global $db;
        $result = $db->select("SELECT * FROM `s_test_make_table`;");

        $this->assertTrue(!empty($result) && is_array($result));
    }

    public function testPreparedSelect() {
        global $db;
        $result = $db->preparedSelect(
            "SELECT * FROM `s_test_make_table` WHERE id=:id",
            [
                "id" => 10
            ]
        );
        
        $this->assertTrue(!empty($result) && is_array($result));
    }

    public function testSelectBy() {
        global $db;
        $result = $db->selectBy(
            "test_make_table",
            [
                "id" => "10"
            ]
        );
        
        $this->assertTrue(!empty($result) && is_array($result));
    }

    public function testResetId() {
        global $db;
        $result = $db->resetId("test_make_table", "id");

        $this->assertTrue($result >= 1);
    }

    public function testInsert() {
        global $db;
        $result = $db->insert(
            "test_create_table",
            [
                "name" => "40"
            ]
        );

        $this->assertNotFalse($result);
    }

    public function testLastInsertId() {
        global $db;
        $result = $db->lastInsertId();

        $this->assertTrue($result >= 1);
    }

    public function testUpdate() {
        global $db;
        $result = $db->update(
            "test_create_table",
            [
                "id" => 10
            ],
            [
                "id" => 40
            ]
        );
        
        $this->assertNotFalse($result);
    }

    public function testDeleteBy() {
        global $db;
        $result1 = $db->deleteBy("test_create_table", []);
        $result2 = $db->deleteBy("test_make_table", []);

        $this->assertTrue($result1 && $result2);
    }

    public function testDropTables() {
        global $db;
        $result1 = $db->query("DROP TABLE `s_test_create_table`");
        $result2 = $db->query("DROP TABLE `s_test_make_table`");
        
        $this->assertTrue($result1 && $result2);
    }
}