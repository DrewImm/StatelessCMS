<?php

namespace Stateless;

/**
 * @brief A single column in a database table.
 */
class DatabaseColumn {
    public $name; /**< Name of the database column */
    public $type; /**< Data type - i.e. "int", "varchar", "varchar(255)" */
    public $default; /**< Default value.  i.e. "TIMESTAMP", etc */
    public $auto; /**< If this should be the primary auto-incrementing key */
    public $size; /**< Data size */
    public $require; /**< If this column should be required (NOT NULL) */
    public $primary; /**< If this column is the table's primary key */

    /**
     * @brief Construct a single DatabaseColumn object to be used with
     *   Connection::createTable()
     * @param string $name Name for the column
     * @param string $type Type of database to store
     * @param boolean $auto If this should be the primary auto-incrementing key
     */
    public function __construct($name, $type, $auto = false) {
        $this->name = $name;
        $this->type = $type;
        $this->auto = $auto;
    }
}