<?php

namespace Stateless;

class Model {

    /**
     * Construct a new Model object
     * 
     * @param Database $db Stateless Database object to pull the Model object
     * @param int $id (Optional) Object ID to pull by
     */
    public function __construct($db = false, $id = false) {

        // Check for ID
        if ($db && $id) {
            $this->pull($db, $id);
        }

    }

    /**
     * Cast this object to an array
     * 
     * @return array Returns this object as an array
     */
    public function toArray() {

        return array_filter((array) $this);

    }

    /**
     * Cast this object from an array
     * 
     * @param array $array Array of key/value fields to populate this
     *  object's members from
     */
    public function fromArray($array) {

        foreach ($array as $key => $value) {

            $this->$key = $value;

        }
        
    }

}
