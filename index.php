<?php
/**
 * Title: Index Controller
 * Purpose: To serve as the entry point of the application that imports all
 *          models and controllers
 */

// Import the data models


// Define a 404 Not Found function
function return404()
{
    $title = '404 Not Found';
    $body = 'That page does not exist.';
    include('views/error.php');
    exit();
}

// Start the session
$lifetime = 60 * 60 * 24 * 365; // 1 year in seconds
session_set_cookie_params($lifetime, '/');
session_start();



// Get the action
$action = filter_input(INPUT_POST, 'action');
if ($action == NULL)
{
    $action = filter_input(INPUT_GET, 'action');
    if ($action == NULL)
    {
        $action = 'home';
    }
}

// Register the controllers
require('Controllers/HomeController.php');

return404();
