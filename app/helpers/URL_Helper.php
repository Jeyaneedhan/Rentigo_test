<?php
// Note: helpers are just regular functions, not classes
// We use them throughout the app to avoid repeating code

// Simple page redirect - makes it easy to send users to different pages
// For example: redirect('tenant/dashboard') sends them to the tenant dashboard
function redirect($page)
{
    header('location: ' . URLROOT . '/' . $page);
}
