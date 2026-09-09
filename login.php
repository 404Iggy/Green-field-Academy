<?php
require_once __DIR__ . '/config.php';
$pageTitle = 'Portal Login';

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        $error = 'Email and password are required.';
    } elseif (loginUser($email, $password)) {
        header('Location: portal.php');
        exit;
    } else {
        $error = 'Invalid email or password.';
    }
}
?>
<?php include __DIR__ . '/header.php'; ?>

<div class="page-hero">
  <div class="container">
    <div class="breadcrumb"><a href="index.php">Home</a> / Portal</div>
    <h1>Portal Login</h1>
    <p>Enter your email and password to access the student or teacher portal.</p>
  </div>
</div>

<section class="section">
  <div class="container" style="max-width:540px; margin:auto;">
    <div class="form-card">
      <h3 style="color:var(--green-dark);margin-bottom:.75rem">Sign In</h3>
      <p style="margin-bottom:1.75rem">Students can view assignments and timetables; teachers can assign work, update schedules and publish school news.</p>

      <?php if ($error): ?><div class="alert alert-error"><?= sanitize($error) ?></div><?php endif; ?>

      <form method="POST" action="">
        <div class="form-group">
          <label for="email">Email Address</label>
          <input type="email" id="email" name="email" placeholder="your@email.com" required
                 value="<?= sanitize($email) ?>">
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" placeholder="Password" required>
        </div>
        <button type="submit" class="btn-submit">Sign In</button>

        <p style="text-align:center;margin-top:1.25rem;font-size:.9rem;color:var(--text-light)">
          Don't have an account?
          <a href="register.php" style="color:var(--green-light);font-weight:600">Create one here</a>
        </p>
      </form>
    </div>
  </div>
</section>

<?php include __DIR__ . '/footer.php'; ?>