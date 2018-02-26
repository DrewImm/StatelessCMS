<?php

namespace Stateless;

/**
 * A single column in a database table.
 */
class DatabaseColumn {

    /** Name of the database column */
    public $name;

    /** Data type - i.e. "int", "varchar", "varchar(255)" */
    public $type;

    /** Default value.  i.e. "TIMESTAMP", etc */
    public $default;

    /** If this should be the primary auto-incrementing key */
    public $auto;

    /** Data size */
    public $size;

    /** If this column should be required (NOT NULL) */
    public $require;

    /** If this column is the table's primary key */
    public $primary;

    /** If the value of this column must be unique */
    public $unique;

    /**
     * Construct a single DatabaseColumn object to be used with
     *   Connection::createTable()
     * 
     * @param mixed $name (string) Name for the column.  (array) Array of key
     *  value pairs to create from
     * @param string $type Type of database to store
     * @param boolean $auto If this should be the primary auto-incrementing key
     */
    public function __construct($name, $type, $auto = false) {

        if (is_string($name)) {
            $this->name = $name;
        }
        else if (is_array($name)) {
            $data = $name;

            if (array_key_exists("name", $data)) {
                $this->name = $data["name"];
            }

            if (array_key_exists("type", $data)) {
                $this->type = $data["type"];
            }

            if (array_key_exists("default", $data)) {
                $this->default = $data["default"];
            }

            if (array_key_exists("auto", $data)) {
                $this->auto = $data["auto"];
            }

            if (array_key_exists("size", $data)) {
                $this->size = $data["size"];
            }

            if (array_key_exists("require", $data)) {
                $this->require = $data["require"];
            }

            if (array_key_exists("primary", $data)) {
                $this->primary = $data["primary"];
            }

            if (array_key_exists("unique", $data)) {
                $this->unique = $data["unique"];
            }

        }

        $this->type = $type;
        $this->auto = $auto;
    }

};
