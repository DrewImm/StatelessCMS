<?php

namespace Stateless;

/**
 * A Controller routes Requests through your application, and shows the View
 *  and Form that was found.
 */
class Controller {

    /** Form to display in the View. */
    public $form;

    /** View to display. */
    public $view;

    /** Role required to access this resource. */
    public $roleRequired;

    /** Error response code to respond with */
    public $response;
    
    /**
     * Route Requests
     */
    public function route() {
        throw new \Exception ("Your Controller must have a route() function");
    }
    
    /**
     * Show the View
     */
    public function show() {
        throw new \Exception ("Your Controller must have a show() function");
    }

    /**
     * Check if a valid View is present in this Controller
     * 
     * @return boolean Returns if a View is present
     */
    public function isValid() {
        return ($this->view);
    }

};
