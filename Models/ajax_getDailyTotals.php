<?php
header('Content-Type: application/json');
require_once '../Models/dbAccess.php';
$db = new dbAccess();

$userId = $_SESSION['UserId'];
$logs   = $db->getFoodLogsByUser($userId);

$nutrByFood = [];
foreach (array_unique(array_column($logs,'food_item_id')) as $fid) {
  $nutrByFood[$fid] = (new FoodApiAccess())->getFood($fid)['foodNutrients'] ?? [];
}

date_default_timezone_set('America/Chicago');
$dailyTotals = [];
foreach ($logs as $log) {
  $dt = new DateTime($log['eaten_at'], new DateTimeZone('UTC'));
  $dt->setTimezone(new DateTimeZone('America/Chicago'));
  $day = $dt->format('l');
  $qty = $log['quantity'];

  foreach ($nutrByFood[$log['food_item_id']] as $n) {
    $name = $n['nutrient']['name'] ?? $n['name'];
    $amt  = ($n['amount'] ?? $n['nutrient']['amount']) * $qty;
    $dailyTotals[$day][$name] = ($dailyTotals[$day][$name] ?? 0) + $amt;
  }
}

echo json_encode($dailyTotals);
