<?php
/**
 * Base Controller
 * THE PARENT CLASS: Every single page controller (like Tenant, Landlord, etc.) 
 * inherits from this. It contains the "Superpowers" they all need.
 */
class Controller
{
    /**
     * Power #1: Load a Model.
     * Use this to talk to the database.
     * Example: $this->userModel = $this->model('M_Users');
     */
    public function model($model)
    {
        // First, include the model file
        require_once '../app/models/' . $model . '.php';

        // Then create and return a new instance of that model
        return new $model();
    }

    /**
     * Power #2: Load a View.
     * This puts the HTML on the screen!
     * 
     * @param string $view - The path to the file (e.g., 'pages/index')
     * @param array $data - The information we want to display on that page
     */
    public function view($view, $data = [])
    {
        // Make sure the view file actually exists before trying to load it
        if (file_exists('../app/views/' . $view . '.php')) {
            require_once '../app/views/' . $view . '.php';
        } else {
            // If the view doesn't exist, stop everything and show an error
            die('View does not exist');
        }
    }
}
