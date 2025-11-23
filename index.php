<?php require __DIR__ . '/config.php';
$symptoms = getAll('SELECT code, name FROM symptoms ORDER BY code');
$diseases  = getMapBy('SELECT code, name FROM diseases', 'code', 'name');
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sistem Pakar ISPA – Forward Chaining</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="container">
    <div class="header">
      <div class="title">Sistem Pakar Diagnosa ISPA</div>
      <a class="btn btn-primary" href="#" onclick="history.back(); return false;">&larr; Kembali</a>
    </div>
    <div class="card" style="padding:14px">
      <p>Pilih gejala yang dialami, lalu tekan tombol Diagnosa.</p>
      <form method="post" action="diagnose.php">
        <div class="grid">
          <?php foreach ($symptoms as $s): ?>
          <label class="symptom">
            <input type="checkbox" name="symptoms[]" value="<?= htmlspecialchars($s['code']) ?>">
            <span><strong><?= htmlspecialchars($s['code']) ?></strong> – <?= htmlspecialchars($s['name']) ?></span>
          </label>
          <?php endforeach; ?>
        </div>
        <div class="footer">
          <span class="muted">Data penyakit: <?= count($diseases) ?> | Gejala: <?= count($symptoms) ?></span>
          <button type="submit" class="btn btn-primary">Diagnosa</button>
        </div>
      </form>
    </div>
  </div>
</body>
</html>