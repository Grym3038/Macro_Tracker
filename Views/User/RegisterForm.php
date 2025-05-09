<?php
// views/User/RegisterForm.php
// Controller should set: $error (string|null)
?>
<?php include('Views/_partials/header.php'); ?>
  <h2>Register New Account</h2>

  <?php if (!empty($error)): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <form method="POST" action=".?action=Register">
    <label>
      Username:<br>
      <input
        type="text"
        name="username"
        required
        maxlength="50"
        value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>"
      >
    </label>
    <br><br>

    <label>
      Email:<br>
      <input
        type="email"
        name="email"
        required
        maxlength="100"
        value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>"
      >
    </label>
    <br><br>

    <label>
      Password:<br>
      <input type="password" name="password" required>
    </label>
    <br><br>

    <button type="submit">Register</button>
  </form>
  <?php include('Views/_partials/footer.php'); ?>