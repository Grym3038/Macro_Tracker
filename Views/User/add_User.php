<?php
require 'dbAccess.php';
$db = new dbAccess();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $newId = $db->createUser(
            trim($_POST['username']),
            trim($_POST['email']),
            $_POST['password']
        );
        // Redirect to the profile form for this new user:
        header("Location: add_user_profile.php?user_id={$newId}");
        exit;
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Register</title></head>
<body>
  <h2>Register New Account</h2>
  <?php if(!empty($error)): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <form method="POST" action="">
    <label>Username:<br>
      <input type="text" name="username" required maxlength="50">
    </label><br><br>

    <label>Email:<br>
      <input type="email" name="email" required maxlength="100">
    </label><br><br>

    <label>Password:<br>
      <input type="password" name="password" required>
    </label><br><br>

    <button type="submit">Register</button>
  </form>
</body>
</html>
