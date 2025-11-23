<?php
require __DIR__ . '/config.php';
$hypotheses = getAll('SELECT id, code, name, description FROM backward_hypotheses ORDER BY name');
function e($s){return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Backward Chaining – Pilih Penyakit</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="container">
    <div class="header">
      <div class="title">Backward Chaining</div>
      <div class="actions">
        <a class="btn" href="index.php">&larr; Kembali (Forward)</a>
      </div>
    </div>

    <div class="card">
      <h2 class="mt-0">Pilih Penyakit</h2>
      <?php if (empty($hypotheses)): ?>
        <p class="muted">Belum ada data penyakit untuk backward. Tambahkan data pada tabel backward_hypotheses dan relasinya.</p>
      <?php else: ?>
        <div class="grid">
          <?php foreach ($hypotheses as $h): ?>
            <div class="card">
              <div style="display:flex; justify-content:space-between; align-items:center; gap:10px;">
                <div>
                  <div style="font-weight:700;"><?= e($h['name']) ?></div>
                  <div class="muted">Kode: <?= e($h['code']) ?></div>
                </div>
                <a class="btn btn-primary" href="backward_detail.php?id=<?= (int)$h['id'] ?>">Lihat Gejala</a>
              </div>
              <?php if (!empty($h['description'])): ?>
                <p class="mt-8 mb-0 muted"><?= e($h['description']) ?></p>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
