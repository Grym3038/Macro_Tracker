<?php
// ajax_createFoodLog.php
// (no output allowed before this point)

// 1) Error reporting (dev only)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 2) Ensure JSON response
header('Content-Type: application/json');

// 3) Force US Central time
date_default_timezone_set('America/Chicago');

// Prevent any prior output (headers, BOM, whitespace)
if (ob_get_length()) { ob_clean(); }


// 4) Load your DB class
require_once '../Models/dbAccess.php';

$db = new dbAccess();

$data = [
  'user_id'      => filter_input(INPUT_POST, 'user_id',      FILTER_VALIDATE_INT),
  'food_item_id' => filter_input(INPUT_POST, 'food_item_id', FILTER_VALIDATE_INT),
  'quantity'     => filter_input(INPUT_POST, 'quantity',     FILTER_VALIDATE_FLOAT),
  'eaten_at'     => (new DateTime('now', new DateTimeZone('America/Chicago')))
                         ->format('Y-m-d H:i:s'),
];

try {
  $newLogId = $db->createFoodLog($data);
  echo json_encode(['success' => true, 'log_id' => $newLogId]);
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}