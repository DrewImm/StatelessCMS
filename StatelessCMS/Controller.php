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

    /** Default 404 View */
    public $default404;

    /**
     * Start the controller
     * 
     * @return boolean Returns if a view was found and shown
     */
    public function start() {

        // Route the controller
        $this->route();

        // Check the view
        if (!$this->view) {

            // Set 404 response
            $this->response = 404;
            $this->view = $default404;

        }

        // Check if a view was set
        if ($this->isValid()) {

            // Show the view
            $this->show();

            return true;
        }

        return false;
    }
    
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

    /**
     * Show the results of the Controller
     */
    public function show() {

        // Check for a view
        if (!$this->view) {

            throw new \Exception("You cannot show your controller without a view");

        }

        // Check for a response
        if ($this->response) {
            Response::header($this->response);
        }

        // Check if the View has a close method
        if (method_exists($this->view, "close")) {

            // Open the view
            $this->view->show();

            // Get output buffer contents
            $output = ob_get_contents();
            ob_clean();
    
            // Output buffer contents
            echo $output;

            // Show the form, if it exists
            if ($this->form) {

                $this->form->show();

                // Show form scripts
                if (method_exists($this->form, "showScripts")) {

                    $this->form->showScripts();

                }

            }

            // Close the view
            $this->view->close();

        }
        else if (method_exists($this->view, "show")) {

            // Show the form, if it exists
            if ($this->form) {

                $this->form->show();

            }

            // Show the view, if it exists
            $this->view->show();

            // Get output buffer contents
            $output = ob_get_contents();
            ob_clean();

            // Output buffer contents
            echo $output;
            
            // Show form scripts, if the form exists
            if ($this->form) {

                $this->form->showScripts();

            }

        }

        // Done

    }

};
