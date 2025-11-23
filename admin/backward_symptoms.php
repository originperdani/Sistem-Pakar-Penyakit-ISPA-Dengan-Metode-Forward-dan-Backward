<?php
require_once __DIR__ . '/../config.php';
$db = db();
require_admin();

// Handle delete
if (isset($_GET['del'])) {
  $code = $_GET['del'];
  $stmt = $db->prepare('DELETE FROM symptoms WHERE code = ?');
  $stmt->execute([$code]);
  header('Location: backward_symptoms.php');
  exit;
}

// Handle create/update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $code = trim($_POST['code'] ?? '');
  $name = trim($_POST['name'] ?? '');
  $desc = trim($_POST['description'] ?? '');

  if ($code && $name) {
    $exists = $db->prepare('SELECT COUNT(*) FROM symptoms WHERE code = ?');
    $exists->execute([$code]);
    if ($exists->fetchColumn() > 0) {
      $stmt = $db->prepare('UPDATE symptoms SET name = ?, description = ? WHERE code = ?');
      $stmt->execute([$name, $desc, $code]);
    } else {
      $stmt = $db->prepare('INSERT INTO symptoms (code, name, description) VALUES (?, ?, ?)');
      $stmt->execute([$code, $name, $desc]);
    }
  }
  header('Location: backward_symptoms.php');
  exit;
}

// Load data
$editCode = $_GET['edit'] ?? null;
$editRow = null;
if ($editCode) {
  $stmt = $db->prepare('SELECT * FROM symptoms WHERE code = ?');
  $stmt->execute([$editCode]);
  $editRow = $stmt->fetch(PDO::FETCH_ASSOC);
}

$rows = $db->query('SELECT * FROM symptoms ORDER BY code')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Kelola Gejala • ISPA Admin (Backward Mode)</title>
  <link rel="stylesheet" href="../style.css" />
</head>
<body>
  <header class="app-header">
    <div class="brand">
      <div class="logo-dot"></div>
      <div class="brand-name">ISPA Clinic • Admin (Backward Mode)</div>
    </div>
    <nav class="app-nav">
      <a href="backward_dashboard.php">Dashboard</a>
      <a href="backward_list.php">Penyakit</a>
      <a class="active" href="backward_symptoms.php">Gejala</a>
      <a href="../index.php">Diagnosa</a>
      <a href="logout.php">Keluar</a>
    </nav>
  </header>

  <main class="container">
    <div class="card" style="margin-bottom:16px">
      <div class="section-header">
        <div class="title"><?= $editRow ? 'Edit Gejala' : 'Tambah Gejala' ?></div>
      </div>
      <form method="post" class="form-grid">
        <div>
          <label class="mb-20">Kode</label>
          <input class="input" name="code" value="<?= htmlspecialchars($editRow['code'] ?? '') ?>" placeholder="G031" required />
        </div>
        <div>
          <label class="mb-20">Nama Gejala</label>
          <input class="input" name="name" value="<?= htmlspecialchars($editRow['name'] ?? '') ?>" placeholder="Contoh: Batuk berdahak" required />
        </div>
        <div style="grid-column:1/-1">
          <label class="mb-20">Deskripsi</label>
          <textarea class="input" name="description" rows="3" placeholder="Deskripsi singkat"><?= htmlspecialchars($editRow['description'] ?? '') ?></textarea>
        </div>
        <div class="actions">
          <button class="btn btn-primary" type="submit">Simpan</button>
          <a class="btn btn-secondary" href="backward_symptoms.php">Batal</a>
        </div>
      </form>
    </div>

    <div class="card">
      <div class="section-header">
        <div class="title">Daftar Gejala</div>
        <a class="btn btn-primary" href="backward_symptoms.php">Tambah Baru</a>
      </div>
      <div style="overflow:auto">
        <table class="table">
          <thead>
            <tr>
              <th>Kode</th>
              <th>Nama</th>
              <th>Deskripsi</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($rows as $r): ?>
            <tr>
              <td><?= htmlspecialchars($r['code']) ?></td>
              <td><?= htmlspecialchars($r['name']) ?></td>
              <td><?= htmlspecialchars($r['description']) ?></td>
              <td class="table-actions">
                <a class="btn" href="?edit=<?= urlencode($r['code']) ?>">Edit</a>
                <a class="btn" href="?del=<?= urlencode($r['code']) ?>" onclick="return confirm('Hapus gejala ini?')">Hapus</a>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>
  <footer class="container footer">
    <div>© ISPA Clinic</div>
    <div class="badge">Manajemen Gejala (Backward Mode)</div>
  </footer>
</body>
</html>
