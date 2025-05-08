<?php
// Views/User/LoginForm.php
// Front-controller will set $error if invalid
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Log In</title>
</head>
<body>
  <?php include __DIR__ . '/../partials/navbar.php'; ?>

  <h2>Log In</h2>
  <?php if (!empty($error)): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <form method="POST" action="?action=Login">
    <label>
      Username:<br>
      <input
        type="text"
        name="username"
        required
        value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>"
      >
    </label>
    <br><br>

    <label>
      Password:<br>
      <input type="password" name="password" required>
    </label>
    <br><br>

    <button type="submit">Log In</button>
  </form>
</body>
</html>
