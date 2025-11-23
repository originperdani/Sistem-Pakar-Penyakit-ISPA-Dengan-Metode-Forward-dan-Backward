<?php
require_once __DIR__ . '/../config.php';

if (is_admin()) { header('Location: dashboard.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $u = trim($_POST['username'] ?? '');
  $p = trim($_POST['password'] ?? '');
  if ($u === ADMIN_USER && $p === ADMIN_PASS) {
    $_SESSION['admin'] = true;
    header('Location: dashboard.php');
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
          <button class="btn btn-primary" type="submit">Masuk</button>
        </div>
      </form>
    </div>
  </main>
</body>
</html>