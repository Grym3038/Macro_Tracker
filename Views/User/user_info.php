<?php
require __DIR__ .  '/../Models/dbAccess.php';
$db = new dbAccess();
$message = '';

// Grab the new user_id from the query string:
$userId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
if (!$userId) {
    die('No user specified. Go back and register first.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'user_id'    => $userId,
        'first_name' => trim($_POST['first_name']),
        'last_name'  => trim($_POST['last_name']),
        'dob'        => $_POST['dob'] ?: null,
        'gender'     => $_POST['gender'] ?: null,
        'height_cm'  => $_POST['height_cm'] !== '' ? (float)$_POST['height_cm'] : null,
        'weight_kg'  => $_POST['weight_kg'] !== '' ? (float)$_POST['weight_kg'] : null,
        'goal'       => trim($_POST['goal']) ?: null,
    ];

    try {
        $db->createProfile($data);
        $message = "✅ Profile saved for user #{$userId}";
    } catch (Exception $e) {
        $message = "❌ Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Complete Your Profile</title></head>
<body>
  <h2>Finish Your Profile</h2>
  <?php if($message): ?>
    <p><?= htmlspecialchars($message) ?></p>
  <?php endif; ?>

  <form method="POST" action="">
    <!-- hide the user_id so they can't change it -->
    <input type="hidden" name="user_id" value="<?= $userId ?>">

    <p>Adding profile for User #<?= $userId ?></p>

    <label>First Name:<br>
      <input type="text" name="first_name" maxlength="50">
    </label><br><br>

    <label>Last Name:<br>
      <input type="text" name="last_name" maxlength="50">
    </label><br><br>

    <label>Date of Birth:<br>
      <input type="date" name="dob">
    </label><br><br>

    <label>Gender:<br>
      <select name="gender">
        <option value="">--Select--</option>
        <option value="male">Male</option>
        <option value="female">Female</option>
        <option value="other">Other</option>
      </select>
    </label><br><br>

    <label>Height (cm):<br>
      <input type="number" name="height_cm" min="0" step="0.1">
    </label><br><br>

    <label>Weight (kg):<br>
      <input type="number" name="weight_kg" min="0" step="0.1">
    </label><br><br>

    <label>Goal:<br>
      <textarea name="goal" rows="2"></textarea>
    </label><br><br>

    <button type="submit">Save Profile</button>
  </form>
</body>
</html>
