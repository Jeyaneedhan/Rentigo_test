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
    return isLoggedIn() && isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'tenant';
}

// Check if the logged-in user is a landlord
function isLandlord()
{
    return isLoggedIn() && isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'landlord';
}

// Check if the logged-in user is a property manager
function isPropertyManager()
{
    return isLoggedIn() && isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'property_manager';
}

// Check if the logged-in user is an admin
function isAdmin()
{
    return isLoggedIn() && isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';
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
 * Generic helper to get statistics for the last 30 days
 * We use this for dashboard stats like "payments in last 30 days"
 * @param object $db Database object
 * @param string $table Table name to query
 * @param string $dateColumn Date column name to filter on
 * @param string $statType 'count' (count rows) or 'sum' (add up values)
 * @param string|null $sumColumn Column to sum if type is 'sum'
 * @param string $where Extra where conditions (optional)
 * @param int $days Number of days to look back
 * @return mixed The calculated stat value
 */
function get30DayStat($db, $table, $dateColumn, $statType = 'count', $sumColumn = null, $where = '', $days = 30)
{
    // Get the date filter SQL
    $dateFilter = getDateRangeSql($dateColumn, $days);
    $sql = "SELECT ";
    
    // Build the SELECT part based on what type of stat we want
    if ($statType === 'count') {
        $sql .= "COUNT(*) as stat";
    } else {
        $sql .= "SUM($sumColumn) as stat";
    }
    
    $sql .= " FROM $table WHERE $dateFilter";
    
    // Add any extra conditions if provided
    if (!empty($where)) {
        $sql .= " AND ($where)";
    }
    
    // Run the query and get the result
    $db->query($sql);
    $result = $db->single();
    
    // Return the stat value, or 0 if nothing found
    return $result ? ($result->stat ?? 0) : 0;
}
