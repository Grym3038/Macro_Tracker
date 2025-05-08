<?php
// Views/User/LoginForm.php
// Front-controller will set $error if invalid
?>

<?php include('Views/_partials/header.php'); ?>
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

  <?php include('Views/_partials/footer.php'); ?>