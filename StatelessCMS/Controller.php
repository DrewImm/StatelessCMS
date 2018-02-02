<?php

namespace Stateless;

/**
 * A Controller routes Requests through your application, and shows the View
 *  and Form that was found.
 */
class Controller {

    /** Subcontroller */
    public $subcontroller;

    /** Form to display in the View. */
    public $form;

    /** View to display. */
    public $view;

    /** Role required to access this resource. */
    public $roleRequired;

    /** User allowed to view this resource, event without specified role */
    public $userAllowed;

    /** Error response code to respond with */
    public $response;
    
    /**
     * Route Requests
     */
    public function route() {

        /**
         * Check for 404
         */
        
        // If we have a subcontroller, route it
        if ($this->subcontroller) {

            // Route it
            $this->subcontroller->route();

            // Now inherit it's values
            $this->inherit();
            $this->response = $this->subcontroller->response;
    
        }
    }

    /**
     * Inherit subcontroller's values
     */
    public function inherit() {
        
        // If we have a subcontroller, inherit it
        if ($this->subcontroller) {

            // Now inherit it's values
            $this->form = $this->subcontroller->form;
            $this->view = $this->subcontroller->view;
    
        }

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
