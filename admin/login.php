<?php
require_once __DIR__ . '/../config.php';

if (is_admin()) { header('Location: dashboard.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $u = trim($_POST['username'] ?? '');
  $p = trim($_POST['password'] ?? '');
  $mode = $_POST['mode'] ?? 'forward'; // assume default mode from login form, needs form update
  if ($u === ADMIN_USER && $p === ADMIN_PASS) {
    session_regenerate_id(true); // regenerate session ID on login success to clear old session data
    $_SESSION['admin'] = true;
    $_SESSION['mode'] = $mode; // save mode in session for dashboard or routing
    if ($mode === 'backward') {
      header('Location: backward_dashboard.php');
    } else {
      header('Location: dashboard.php');
    }
    exit;
  } else {
    $error = 'Username atau password salah';
  }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login Admin • ISPA</title>
  <link rel="stylesheet" href="../style.css" />
</head>
<body>
  <header class="app-header">
    <div class="brand">
      <div class="logo-dot"></div>
      <div class="brand-name">ISPA Clinic • Admin</div>
    </div>
  </header>
  <main class="container">
    <div class="card" style="max-width:520px;margin:40px auto">
      <div class="title">Masuk Admin</div>
      <?php if ($error): ?>
        <div class="badge" style="margin:10px 0;color:#ef4444;border-color:#ef4444"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>
      <form method="post" class="form-grid">
        <div>
          <label>Username</label>
          <input class="input" name="username" placeholder="admin" required />
        </div>
        <div>
          <label>Password</label>
          <input class="input" type="password" name="password" placeholder="••••••" required />
        </div>
        <div>
          <label>Mode</label>
          <select name="mode" class="input" required>
            <option value="forward" selected>Forward Chaining</option>
            <option value="backward">Backward Chaining</option>
          </select>
        </div>
        <div>
          <button class="btn btn-primary" type="submit">Masuk</button>
        </div>
      </form>
    </div>
  </main>
</body>
</html>