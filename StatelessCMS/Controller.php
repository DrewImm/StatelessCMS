<?php

namespace Stateless;

/**
 * A Controller routes Requests through your application, and shows the View
 *  and Form that was found.
 */
class Controller {

    /** Form to display in the View. */
    protected $form;

    /** View to display. */
    protected $view;

    /** Role required to access this resource. */
    protected $roleRequired;

    /** Error response code to respond with */
    protected $response;
    
    /**
     * Route Requests
     */
    protected function route() {
        throw new \Exception ("Your Controller must have a route() function");
    }
    
    /**
     * Show the View
     */
    protected function show() {
        throw new \Exception ("Your Controller must have a show() function");
    }

    /**
     * Check if a valid View is present in this Controller
     * 
     * @return boolean Returns if a View is present
     */
    protected function isValid() {
        return ($this->view);
    }

};
