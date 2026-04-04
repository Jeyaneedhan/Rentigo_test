<?php
// Check if someone is logged in by looking for their user_id in the session
function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

/**
 * ROLE CHECKS: Check if the logged-in person is a certain type of user.
 * We use these to show/hide different parts of the dashboard.
 */
function isTenant()
{
    return isLoggedIn() &&
        isset($_SESSION['user_type']) &&
        $_SESSION['user_type'] === 'tenant';
}

// Check if the logged-in user is a landlord
function isLandlord()
{
    return isLoggedIn() &&
        isset($_SESSION['user_type']) &&
        $_SESSION['user_type'] === 'landlord';
}

// Check if the logged-in user is a property manager
function isPropertyManager()
{
    return isLoggedIn() &&
        isset($_SESSION['user_type']) &&
        $_SESSION['user_type'] === 'property_manager';
}

// Check if the logged-in user is an admin
function isAdmin()
{
    return isLoggedIn() &&
        isset($_SESSION['user_type']) &&
        $_SESSION['user_type'] === 'admin';
}

/**
 * THE LOGOUT BUTTON: Clear all session data and kick them back to login.
 */
function logout()
{
    // Remove all user-related session variables
    unset($_SESSION['user_id']);
    unset($_SESSION['user_email']);
    unset($_SESSION['user_name']);
    unset($_SESSION['user_type']);

    // Destroy the entire session
    session_destroy();

    // Send them back to the login page
    redirect('users/login');
}

/**
 * DATE MAGIC: Generates SQL to only look at data from the last X days.
 * This is how the dashboard charts stay up to date.
 */
function getDateRangeSql($columnName, $days = 30)
{
    return " $columnName >= DATE_SUB(CURDATE(), INTERVAL $days DAY) ";
}

/**
 * PERIOD FILTER: Generates SQL date condition based on period selection.
 * Used by stat card dropdowns for filtering by 'all', 'month', or 'year'.
 * @param string $columnName The date column to filter on
 * @param string $period 'all' (no filter), 'month' (current month), 'year' (current year)
 * @return string SQL condition string (empty for 'all', date filter for others)
 */
function getDateRangeByPeriod($columnName, $period = 'all')
{
    switch ($period) {
        case 'month':
            // Current calendar month
            return " $columnName >= DATE_FORMAT(CURDATE(), '%Y-%m-01') ";
        case 'year':
            // Current calendar year
            return " $columnName >= DATE_FORMAT(CURDATE(), '%Y-01-01') ";
        case 'all':
        default:
            // No date filter - return always true condition
            return " 1=1 ";
    }
}
