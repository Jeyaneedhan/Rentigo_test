<?php
/**
 * TimeConvert Helper
 * This takes those scary database timestamps (2023-10-27 14:05:32) 
 * and turns them into something humans actually say.
 */

// Rentigo is based in Sri Lanka, so we set the timezone to Colombo!
date_default_timezone_set('Asia/Colombo');

/**
 * THE "TIME AGO" FUNCTION:
 * This is like what you see on social media (e.g., "3 hours ago")
 * It calculates the difference between RIGHT NOW and when something happened.
 */
function convertedToReadableTimeFormat($time)
{
    $time_ago = strtotime($time);
    $current_time = time();
    $time_diffrence = $current_time - $time_ago;

    // We break the total seconds down into units. 
    // This is simple math: 60s per minute, 3600s per hour, etc.
    $seconds = $time_diffrence;
    $minutes = round($seconds / 60);        // 60 seconds in a minute
    $hours = round($seconds / 3600);        // 60 * 60 seconds in an hour
    $days = round($seconds / 86400);        // 24 * 60 * 60 seconds in a day
    $weeks = round($seconds / 604800);      // 7 * 24 * 60 * 60 seconds in a week
    $months = round($seconds / 2629440);    // Average month length (accounting for leap years)
    $years = round($seconds / 31553280);    // Average year length (accounting for leap years)

    // Return the most appropriate time format based on how long ago it was
    if ($seconds <= 60) {
        return 'Just now';
    } else if ($minutes <= 60) {
        if ($minutes == 1) {
            return 'one minute ago';
        } else {
            return $minutes . ' minutes ago';
        }
    } else if ($hours <= 24) {
        if ($hours == 1) {
            return 'an hour ago';
        } else {
            return $hours . ' hours ago';
        }
    } else if ($days <= 7) {
        if ($days == 1) {
            return "yesterday";
        } else {
            return $days . ' days ago';
        }
    } else if ($weeks <= 4.3) { // 4.3 weeks = roughly a month (52 weeks / 12 months)
        if ($weeks == 1) {
            return 'a week ago';
        } else {
            return $weeks . 'weeks ago';
        }
    } else if ($months <= 12) {
        if ($months == 1) {
            return 'a month ago';
        } else {
            return $months . ' months ago';
        }
    } else {
        // If it's been more than a year
        if ($years == 1) {
            return "one year ago";
        } else {
            return $years . ' years ago';
        }
    }
}
