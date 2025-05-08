<?php
/**
 * Title: Home Controller
 * Purpose: To view home page and any other actions
 */

switch ($action)
{
    /**
     * List all albums
     */
    case 'home':
        include('Views/IndexView.php');
        exit();
    
    }
