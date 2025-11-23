<?php
require __DIR__ . '/config.php';

if (($_SESSION['mode'] ?? 'backward') !== 'backward') {
    header('Location: index.php');
    exit;
}

function e($s) {
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

$diseases = getAll('SELECT code, name, description FROM diseases ORDER BY name');
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Backward Chaining – Pilih Penyakit</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <div class="container">
    <div class="header">
      <div class="title">Backward Chaining</div>
      <div class="actions">
        <a class="btn btn-primary" href="<?= base_path('/admin/dashboard.php') ?>">&larr; Kembali</a>
      </div>
    </div>
    <div class="card">
      <h2 class="mt-0">Pilih Penyakit</h2>
      <?php if (empty($diseases)): ?>
        <p class="muted">Belum ada data penyakit.</p>
      <?php else: ?>
        <div class="grid">
          <?php foreach ($diseases as $d): ?>
            <div class="card">
              <div style="display:flex; justify-content:space-between; align-items:center; gap:10px;">
                <div style="min-width: 150px;">
                  <div style="font-weight:700;"><?= e($d['name']) ?></div>
                  <div class="muted">Kode: <?= e($d['code']) ?></div>
                </div>
                <div style="display:flex; gap:10px;">
                  <a class="btn btn-secondary" href="<?= base_path('/backward_fc_detail.php?id=') . urlencode($d['code']) ?>">Pengecekan</a>
                </div>
              </div>
              <?php if (!empty($d['description'])): ?>
                <p class="mt-8 mb-0 muted"><?= e($d['description']) ?></p>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
