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

    /**
     * Construct a single DatabaseColumn object to be used with
     *   Connection::createTable()
     * 
     * @param string $name Name for the column
     * @param string $type Type of database to store
     * @param boolean $auto If this should be the primary auto-incrementing key
     */
    public function __construct($name, $type, $auto = false) {
        $this->name = $name;
        $this->type = $type;
        $this->auto = $auto;
    }

};
