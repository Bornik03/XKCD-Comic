<?php

// Temporarily add these for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Capture environment variables
$env_output = print_r($_SERVER, true);
$env_output .= "\n\n";
$env_output .= "--- getenv() ---\n";
$env_output .= print_r(getenv(), true);

// Log the environment to the cron.log
file_put_contents(__DIR__ . '/../cron.log', $env_output . "\n\n", FILE_APPEND);

// Now include your original code
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/functions.php';

// Original cron job logic
sendXKCDUpdatesToSubscribers();

// Add success message
file_put_contents(__DIR__ . '/../cron.log', "Cron job completed successfully at: " . date("Y-m-d H:i:s") . "\n", FILE_APPEND);
