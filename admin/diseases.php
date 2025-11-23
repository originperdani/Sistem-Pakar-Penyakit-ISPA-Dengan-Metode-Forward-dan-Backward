<?php
require_once __DIR__ . '/../config.php';
$db = db();
require_admin();

// Handle delete
if (isset($_GET['del'])) {
  $code = $_GET['del'];
  $stmt = $db->prepare('DELETE FROM diseases WHERE code = ?');
  $stmt->execute([$code]);
  header('Location: diseases.php');
  exit;
}

// Handle create/update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $code = trim($_POST['code'] ?? '');
  $name = trim($_POST['name'] ?? '');
  $desc = trim($_POST['description'] ?? '');

  if ($code && $name) {
    // upsert
    $exists = $db->prepare('SELECT COUNT(*) FROM diseases WHERE code = ?');
    $exists->execute([$code]);
    if ($exists->fetchColumn() > 0) {
      $stmt = $db->prepare('UPDATE diseases SET name = ?, description = ? WHERE code = ?');
      $stmt->execute([$name, $desc, $code]);
    } else {
      $stmt = $db->prepare('INSERT INTO diseases (code, name, description) VALUES (?, ?, ?)');
      $stmt->execute([$code, $name, $desc]);
    }
  }
  header('Location: diseases.php');
  exit;
}

// Load data
$editCode = $_GET['edit'] ?? null;
$editRow = null;
if ($editCode) {
  $stmt = $db->prepare('SELECT * FROM diseases WHERE code = ?');
  $stmt->execute([$editCode]);
  $editRow = $stmt->fetch(PDO::FETCH_ASSOC);
}

$rows = $db->query('SELECT * FROM diseases ORDER BY code')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Kelola Penyakit • ISPA Admin</title>
  <link rel="stylesheet" href="../style.css" />
</head>
<body>
  <header class="app-header">
    <div class="brand">
      <div class="logo-dot"></div>
      <div class="brand-name">ISPA Clinic • Admin</div>
    </div>
    <nav class="app-nav">
      <a href="dashboard.php">Dashboard</a>
      <a class="active" href="diseases.php">Penyakit</a>
      <a href="symptoms.php">Gejala</a>
      <!-- <a href="rules.php">Aturan</a> -->
      <?php
        $mode = $_SESSION['mode'] ?? 'forward';
      ?>
      <a href="<?php echo ($mode === 'backward') ? '../backward_fc_list.php' : '../index.php'; ?>">Diagnosa</a>
      <a href="logout.php">Keluar</a>
    </nav>
  </header>

  <main class="container">
    <div class="card" style="margin-bottom:16px">
      <div class="section-header">
        <div class="title"><?php echo $editRow ? 'Edit Penyakit' : 'Tambah Penyakit'; ?></div>
      </div>
      <form method="post" class="form-grid">
        <div>
          <label class="mb-20">Kode</label>
          <input class="input" name="code" value="<?php echo htmlspecialchars($editRow['code'] ?? '') ?>" placeholder="P010" required />
        </div>
        <div>
          <label class="mb-20">Nama Penyakit</label>
          <input class="input" name="name" value="<?php echo htmlspecialchars($editRow['name'] ?? '') ?>" placeholder="Contoh: Pneumonia" required />
        </div>
        <div style="grid-column:1/-1">
          <label class="mb-20">Deskripsi</label>
          <textarea class="input" name="description" rows="3" placeholder="Deskripsi singkat"><?php echo htmlspecialchars($editRow['description'] ?? '') ?></textarea>
        </div>
        <div class="actions">
          <button class="btn btn-primary" type="submit">Simpan</button>
          <a class="btn btn-secondary" href="diseases.php">Batal</a>
        </div>
      </form>
    </div>

    <div class="card">
      <div class="section-header">
        <div class="title">Daftar Penyakit</div>
        <a class="btn btn-primary" href="diseases.php">Tambah Baru</a>
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
              <td><?php echo htmlspecialchars($r['code']); ?></td>
              <td><?php echo htmlspecialchars($r['name']); ?></td>
              <td><?php echo htmlspecialchars($r['description']); ?></td>
              <td class="table-actions">
                <a class="btn" href="?edit=<?php echo urlencode($r['code']); ?>">Edit</a>
                <a class="btn" href="?del=<?php echo urlencode($r['code']); ?>" onclick="return confirm('Hapus penyakit ini?')">Hapus</a>
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
    <div class="badge">Manajemen Penyakit</div>
  </footer>
</body>
</html>