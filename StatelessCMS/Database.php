<?php

namespace Stateless;

/**
 * A database connection
 */
class Database {

    private $conn;
    private $server;
    private $username;
    private $password;
    private $dbname;
    private $prefix;
    private $charset;
    private $isActive;
        
    /**
     * Creates a database connection and connects to it
     * 
     * @param string $server Database server address
     * @param string $username Username to connect as
     * @param string $password Password to connect with
     * @param string $dbname Database name to connect to
     * @param string $prefix Prefix to prepend to database table names.
     * @param string $charset Character set to use.  Default is `utf8`
     * @return mixed Returns $this on success, or false on error.
     */
    public function __construct(
        $server, $username, $password, $dbname, $prefix = "", $charset = "utf8"
    ) {
        $this->server = $server;
        $this->username = $username;
        $this->password = $password;
        $this->dbname = $dbname;
        $this->prefix = $prefix;
        $this->charset = $charset;
        $this->isActive = false;

        $this->connect(
            $this->server,
            $this->username,
            $this->password,
            $this->dbname,
            $this->prefix,
            $this->charset
        );
    }
        
    /**
     * Connect to a database
     * 
     * @param string $server Database server address
     * @param string $username Username to connect as
     * @param string $password Password to connect with
     * @param string $dbname Database name to connect to
     * @param string $prefix Prefix to prepend to database table names
     * @param string $charset Character set to use.  Default is `utf8`
     * @return mixed Returns $this on success, or false on error.
     */
    public function connect(
        $server, $username, $password, $dbname, $prefix, $charset = "utf8"
    ) {
        // Build PDO object
        $dsn = "mysql:host=$this->server;" .
            "dbname=$this->dbname;" .
            "charset=$this->charset";
        
        $opt = [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES => false
        ];

        // Connect
        try {
            $this->conn = new \PDO(
                $dsn,
                $this->username,
                $this->password,
                $opt
            );
        }
        catch (\Exception $e) {
            return false;
        }

        $this->isActive = true;

        return $this;
    }

    /**
     * Check if the database connection is active
     * 
     * @return boolean Returns true if active, otherwise false
     */
    public function isActive() {
        return $this->isActive;
    }

    /**
     * Get information about the last error which occurred
     * 
     * @return string Description of last database error
     */
    public function error() {
        return $this->conn->errorInfo();
    }

    /**
     * Run an unprepared sql statement on the database
     * 
     * @param string $query The sql query to run
     * @return mixed Returns results on success, false on failure
     */
    public function query($query) {
        // Execute the statement and return the result
        return $this->conn->query($query);
    }

    /**
     * Run a prepared sql statements and bind the payload
     * 
     * @param string $query The sql query to run
     * @param array $values Array of values to bind to the query.  Default is
     *   empty
     * @return mixed Returns results on success, false on failure
     */
    public function preparedQuery($query, $values = array()) {
        $result = $this->conn->prepare($query);
        $result->execute($values);

        // Execute the statement and return the result
        return $result;
    }

    /**
     * Create a table in the database if it doesn't already exist
     * 
     * @param string $table Name of table to be created
     * @param array $columns Array of DatabaseColumn objects
     * @return mixed Returns if the table was created successfully
     */
    public function createTable($table, $columns) {
        $table = $this->cleanTable($table);
        $query = "CREATE TABLE IF NOT EXISTS $table (";
        $key = false;

        // Add columns
        foreach ($columns as $column) {
            $query .= "`$column->name` $column->type";

            // Column size
            if (!empty($column->size)) {
                $query .= "(" . $column->size . ")";
            }

            // Column auto-increment
            if ($column->auto && strtolower($column->type) === "int") {
                $query .= " AUTO_INCREMENT";
                $column->require = true;
                $column->primary = true;
            }

            // Column is primary
            if ($column->primary) {
                $key = $column->name;
            }

            // Column required
            if ($column->require) {
                $query .= " NOT NULL";
            }

            // Default value
            if (isset($this->default) && !empty($this->default)) {
                $query .= " DEFAULT " . $this->default;
            }

            // Prepare statement for next iteration
            $query .= ",";
        }

        // Check if a key column exists
        if (!empty($key)) {
            $query .= " PRIMARY KEY (`" . $key . "`)";
        }
        else {
            $query = rtrim($query, ",");
        }

        // Closeup sql statement
        $query .= ")";

        // Execute statement and return results
        return $this->query($query);
    }

    /**
     * Query the number of rows in a table
     * 
     * @param string $table Name of the table to query
     * @return mixed Returns the number of rows, or false on failure
     */
    public function nRows($table) {
        $count = false;
        $table = $this->cleanTable($table);

        $results = $this->select(
            "SELECT COUNT(*) FROM " . $table
        );

        if (is_array($results) &&
            count($results) &&
            array_key_exists("COUNT(*)", $results[0])) {
            $count = $results[0]["COUNT(*)"];
        }

        return $count;
    }

    /**
     * Query the number of rows in a table where an array matches
     * 
     * @param string $table Name of the table to query
     * @param array $where Array of key => value pairs to search by
     * @return mixed Returns the number of rows, or false on failure
     */
    public function nRowsWhere($table, $where) {
        $count = false;

        $results = $this->selectBy($table, $where, false, [], "COUNT(*)");
        
        if (is_array($results) &&
            count($results) &&
            array_key_exists("COUNT(*)", $results[0])) {
            $count = $results[0]["COUNT(*)"];
        }

        return $count;
    }

    /**
     * Query the database for matching rows.  This function does not
     *   prepare the payload
     * 
     * @param string $query The sql query to run
     * @return mixed Returns the matching rows as an associative array, or
     *   false on failure.
     */
    public function select($query) {
        $rows = false;
        $result = $this->preparedQuery($query);

        // Check results
        if ($result !== false) {
            // Fetch all columns
            $rows = $result->fetchAll();
        }

        // Return result
        return $rows;
    }

    /**
     * Query the database for matching rows.  This function does prepare
     * 
     * @param string $query The sql query to run
     * @param array $where Array of values to select by.  Default is
     *   all rows
     * @return mixed Returns the matching rows as an associative array, or
     *   false on failure.
     */
    public function preparedSelect($query, $where = array()) {
        $rows = false;
        $result = $this->preparedQuery($query, $where);

        // Check results
        if ($result !== false) {
            // Fetch all columns
            $rows = $result->fetchAll();
        }

        // Return result
        return $rows;
    }

    /**
     * Select by key/value pairs
     * 
     * @param string $table The database table name to search
     * @param mixed $where Array of key/value pairs to search for, or a string
     *   of where clauses.  If an empty array is passed, all rows will be
     *   retrieved.  Default is all rows.
     * @param string $append Extra sql to append to the end of the statement.
     *   Default is none.
     * @param array $values Array of values to bind to a string $where or
     *   $append clause (or both).  Default is an empty array.
     * @param string $select What to selet.  Default is "*".  This should be
     *   sanitized first.
     * @return mixed Returns the matching rows as key/value pairs, or false on
     *   failure.
     */
    public function selectBy(
        $table,
        $where = array(),
        $append = false,
        $values = array(),
        $select = "*"
    ) {
        $table = $this->cleanTable($table);
        $rows = false;
        $query = "SELECT " . $select . " FROM " . $table;

        // Create where clause
        if (!empty($where)) {
            $query .= " WHERE (";

            if (is_array($where)) {
                $i = 0;
                $length = count($where) -1;
                foreach ($where as $key => $value) {
                    $query .= $key;
                    $query .= "=:" . $key;

                    if ($i < $length) {
                        $query .= " AND ";

                    }

                    $i++;
                }
            }
            else if (is_string($where)) {
                $query .= $where;
                $where = array();
            }

            $query .= ")";
        }

        // Append sql
        if (!empty($append) && is_string($append)) {
            $query .= " " . $append;
        }

        // Merge values
        if (!empty($values)) {
            $where = array_merge($where, $values);
        }

        // Execute statement
        return $this->preparedSelect($query, $where);
    }

    /**
     * Resets row's id to the largest current id
     * 
     * @param string $table The name of the database table
     * @param string $idKey The key of the id column for this table
     * @return mixed Returns the highest id, or 1 if no rows exist
     */
    public function resetId($table, $idKey = "id") {
        $table = $this->cleanTable($table);
        $query = "SELECT @max := MAX(" . $idKey . ")+ 1 FROM " . $table . ";";
        $id = $this->query($query);

        // Get the id
        if (!empty($id)) {
            $id = $id->fetchAll();

            if (array_key_exists(0, $id)) {
                foreach ($id[0] as $d) {
                    $id = $d;
                    continue;
                }
            }
        }

        // Check for empty id
        if (empty($id) || !is_int($id)) {
            $id = 1;
        }

        // Reset the id
        $query = "ALTER TABLE " . $table . " AUTO_INCREMENT = " . $id;
        $query = $this->preparedQuery($query);

        // Return the id
        return $id;
    }
    
    /**
     * Insert a row into the database
     * 
     * @param string $table Database table to insert into
     * @param array $payload Key/value pairs of data to insert
     * @return mixed Returns non-false on success, false on failure
     */
    public function insert($table, $payload) {
        $table = $this->cleanTable($table);
        $query = "INSERT INTO " . $table . " (";

        // Loop through payload keys
        foreach ($payload as $key => $value) {
            $query .= $key . ",";
        }
        $query = rtrim($query, ",");

        // Close key clause, start values
        $query .= ") VALUES (";

        // Loop through payload values
        foreach ($payload as $key => $value) {
            $query .= ":" . $key . ",";
        }
        $query = rtrim($query, ",");

        // Close query
        $query .= ")";

        // Merge the payload with itself so PDO can traverse through it
        //   twice - for keys and for values
        $payload = array_merge($payload, $payload);

        // Execute the query and return results
        return $this->preparedQuery($query, $payload);
    }

    /**
     * Get the last insert Id
     * 
     * @return integer Returns the last insert Id, or 0 if none exist
     */
    public function lastInsertId() {
        return $this->conn->lastInsertId();
    }

    /**
     * Update a row in the table
     * 
     * @param string $table The db table to update a row in
     * @param array $payload Key/value pairs to update to
     * @param array $where Key/value pairs to search by
     * @return mixed Returns non-false on success, or false on error
     */
    public function update($table, $payload, $where) {
        $table = $this->cleanTable($table);
        $query = "UPDATE " . $table . " SET ";

        // Loop through payload keys
        foreach ($payload as $key => $value) {
            $query .= $key . " = ?, ";
        }
        $query = rtrim($query, ", ");

        // Loop through where keys
        if (!empty($where)) {
            $query .= " WHERE (";

            $i = 0;
            $length = count($where) - 1;
            // Loop through where keys
            foreach ($where as $key => $value) {
                $query .= $key . " = ?";

                if ($i < $length) {
                    $query .= " AND ";
                }
            }

            $query .= ")";
        }

        // Merge array for preparation
        $prep = array_merge(array_values($payload), array_values($where));

        // Execute query and return results
        return $this->preparedQuery($query, $prep);
    }

    /**
     * Delete rows from table
     * 
     * @param string $table Table to delete from
     * @param array $where Key/value pairs to search for
     * @return mixed Returns non-false on success, or false on error
     */
    public function deleteBy($table, $where) {
        $table = $this->cleanTable($table);
        $query = "DELETE FROM " . $table;

        if (!empty($where)) {
            $query .= " WHERE (";
            
            // Loop through where keys
            $i = 0;
            $length = count($where) - 1;
            foreach ($where as $key => $value) {
                $query .= $key . "=:" . $key;

                if ($i < $length) {
                    $query .= " AND ";
                }
            }
            $query .= ")";
        }

        // Execute the query and return the results
        return $this->preparedQuery($query, $where);
    }

    /**
     * Clean the table name and prepend the table prefix
     * 
     * @param string $table Table name to clean
     */
    public function cleanTable($table) {

        return "`" . $this->prefix . $table . "`";

    }

};
