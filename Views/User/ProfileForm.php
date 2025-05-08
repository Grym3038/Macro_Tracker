<?php
// views/User/ProfileForm.php
// Controller should set: $userId (int), $message (string|null)
?>
<?php include('Views/_partials/header.php'); ?>
  <h2>Finish Your Profile</h2>

  <?php if (!empty($message)): ?>
    <p><?= htmlspecialchars($message) ?></p>
  <?php endif; ?>

  <form method="POST" action=".?action=SaveProfile">
    <!-- keep user_id hidden -->
    <input type="hidden" name="user_id" value="<?= (int)$userId ?>">

    <p>Adding profile for User #<?= (int)$userId ?></p>

    <label>
      First Name:<br>
      <input
        type="text"
        name="first_name"
        maxlength="50"
        value="<?= isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : '' ?>"
      >
    </label>
    <br><br>

    <label>
      Last Name:<br>
      <input
        type="text"
        name="last_name"
        maxlength="50"
        value="<?= isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : '' ?>"
      >
    </label>
    <br><br>

    <label>
      Date of Birth:<br>
      <input
        type="date"
        name="dob"
        value="<?= isset($_POST['dob']) ? htmlspecialchars($_POST['dob']) : '' ?>"
      >
    </label>
    <br><br>

    <label>
      Gender:<br>
      <select name="gender">
        <option value="" <?= empty($_POST['gender']) ? 'selected' : '' ?>>--Select--</option>
        <option value="male"   <?= (($_POST['gender'] ?? '')==='male')   ? 'selected' : '' ?>>Male</option>
        <option value="female" <?= (($_POST['gender'] ?? '')==='female') ? 'selected' : '' ?>>Female</option>
        <option value="other"  <?= (($_POST['gender'] ?? '')==='other')  ? 'selected' : '' ?>>Other</option>
      </select>
    </label>
    <br><br>

    <label>
      Height (cm):<br>
      <input
        type="number"
        name="height_cm"
        min="0"
        step="0.1"
        value="<?= isset($_POST['height_cm']) ? htmlspecialchars($_POST['height_cm']) : '' ?>"
      >
    </label>
    <br><br>

    <label>
      Weight (kg):<br>
      <input
        type="number"
        name="weight_kg"
        min="0"
        step="0.1"
        value="<?= isset($_POST['weight_kg']) ? htmlspecialchars($_POST['weight_kg']) : '' ?>"
      >
    </label>
    <br><br>

    <label>
      Goal:<br>
      <textarea name="goal" rows="2"><?= isset($_POST['goal']) ? htmlspecialchars($_POST['goal']) : '' ?></textarea>
    </label>
    <br><br>

    <button type="submit">Save Profile</button>
  </form>
  <?php include('Views/_partials/footer.php'); ?>