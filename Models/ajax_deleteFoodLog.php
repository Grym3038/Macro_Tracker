<?php
    // ajax_createFoodLog.php

    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    
    if (ob_get_length()) { ob_clean(); }
    
    // Tell the client it’s JSON
    header('Content-Type: application/json');

    // Don’t include header.php or any HTML here!
    // Only include your model
    require_once '../Models/dbAccess.php';
    
    $db = new dbAccess();

  $Logid = filter_input(INPUT_POST, 'logID',      FILTER_VALIDATE_INT);

try {
  $success = $db->deleteFoodLog($Logid);
  echo json_encode(['success' => $success, 'msg' => 'it worked']);
 
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}