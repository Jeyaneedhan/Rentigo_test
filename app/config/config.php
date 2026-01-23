<?php
// Database parameters - these are for connecting to our MySQL database
// Make sure these match your MAMP settings
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', 'root');
define('DB_NAME', 'rentigo_db');

// App root - this gives us the path to the app folder so we can include files properly
define('APPROOT', dirname(dirname(__FILE__)));

// Public Root - this points to where our CSS, JS, and images are stored
define('PUBROOT', dirname(dirname(dirname(__FILE__))) . '\public');

// URL root - this is the base URL for the app, we use it for redirects and links
define('URLROOT', 'http://localhost/Rentigo_test');

// Site name - just the name of our app, used in page titles and stuff
define('SITENAME', 'Rentigo');
