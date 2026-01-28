<?php
// This is the main bootloader - it loads all the essential files we need for the app to work
// We're loading these in a specific order because some files depend on others

// Load helpers first - these are utility functions we use throughout the app
require_once 'helpers/helper.php';
require_once 'helpers/Validator.php';
require_once 'helpers/URL_Helper.php';
require_once 'helpers/Session_Helper.php';
require_once 'helpers/TimeConvert_Helper.php';
require_once 'helpers/Email_Helper.php';

// Load configurations next - this sets up database credentials and app settings
require_once 'config/config.php';

// Load core libraries last - these need the config to be loaded first
require_once 'libraries/Controller.php';
require_once 'libraries/Core.php';
require_once 'libraries/Database.php';
