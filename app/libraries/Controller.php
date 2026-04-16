<?php

/**
 * Base Controller
 */
class Controller
{
    /**
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
     * This puts the HTML on the screen!
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
