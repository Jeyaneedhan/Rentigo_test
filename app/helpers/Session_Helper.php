<?php
// Start the session so we can use $_SESSION throughout the app
session_start();

// Flash message helper - this lets us show one-time messages to users
// For example, "Successfully registered!" after signing up
// EXAMPLE - flash('register_success', 'You are now registered', 'alert alert-success');
// DISPLAY IN VIEW - <?php echo flash('register_success');
function flash($name = '', $message = '', $class = 'msg-flash')
{
    if (!empty($name)) {
        // If we're setting a new flash message
        if (!empty($message) && empty($_SESSION[$name])) {
            // First, clear out any old messages with the same name, just to be safe
            if (!empty($_SESSION[$name])) {
                unset($_SESSION[$name]);
            }

            if (!empty($_SESSION[$name . '_class'])) {
                unset($_SESSION[$name . '_class']);
            }

            // Now save the new message and its CSS class
            $_SESSION[$name] = $message;
            $_SESSION[$name . '_class'] = $class;
        } 
            // DISPLAY MODE: If we only provided a $name, we want to show the logic.
            $class = !empty($_SESSION[$name . '_class']) ? $_SESSION[$name . '_class'] : '';
            echo '<div class="' . $class . '" id="' . $class . '">' . $_SESSION[$name] . '</div>';
            
            // IMPORTANT: Unset the values so the message disappears when they refresh.
            unset($_SESSION[$name]);
            unset($_SESSION[$name . '_class']);
        }
    }
}
