<?php

/**
 * Core Class
 * 
 * URL Format: /controller/method/params
 * Example:    /tenant/view_house/12
 *             - controller: tenant
 *             - method: view_house
 *             - params: 12
 */
class Core
{
    // Default controller if none is specified in the URL
    protected $currentController = 'Pages';
    protected $currentMethod = 'index';
    protected $params = [];

    public function __construct()
    {
        $url = $this->getURL();
        // print_r($this->getURL());

        /* ---------- STEP 1: FIND THE CONTROLLER ---------- */
        // ucwords() is used because our filenames start with capitals (e.g., Tenant.php)

        if (file_exists('../app/controllers/' . ucwords($url[0]) . '.php')) {
            // Found the controller file, so let's use it
            $this->currentController = ucwords($url[0]);

            // Remove the controller from the URL array since we've already processed it
            unset($url[0]);

            require_once '../app/controllers/' . $this->currentController . '.php';

            $this->currentController = new $this->currentController;


            /* ---------- PART 2 - FINDING THE METHOD ---------- */
            if (isset($url[1])) {
                // Make sure this method actually exists in the controller
                if (method_exists($this->currentController, $url[1])) {
                    $this->currentMethod = $url[1];

                    // Remove the method from the URL array too
                    unset($url[1]);
                }
            }

            /* ---------- PART 3 - GRABBING THE PARAMETERS ---------- */
            $this->params = $url ? array_values($url) : [];

            // This line says: "Go to $currentController, run $currentMethod, and give it these $params".
            call_user_func_array([$this->currentController, $this->currentMethod], $this->params);
        }
    }

    /**
     * Helper: This cleans up the raw URL string from the browser 
     * and turns it into a nice array [0 => "controller", 1 => "method", ...]
     */
    public function getURL()
    {
        if (isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');                // Remove trailing "/"
            $url = filter_var($url, FILTER_SANITIZE_URL);   // Security first! Clean the string
            $url = explode('/', $url);                      // Break into array
            return $url;
        }
    }
}
