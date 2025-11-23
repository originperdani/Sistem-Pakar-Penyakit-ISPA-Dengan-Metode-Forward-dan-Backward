<?php
require __DIR__ . '/config.php';

$selected = $_POST['symptoms'] ?? [];
if (!is_array($selected)) $selected = [];
$selected = array_values(array_unique($selected));

$diseases = getMapBy('SELECT code, name FROM diseases', 'code', 'name');
$rules = getAll('SELECT code, disease_code FROM rules');

// Ambil gejala untuk tiap rule
$ruleSymptoms = [];
foreach ($rules as $r) {
  $rows = getAll('SELECT symptom_code FROM rule_symptoms WHERE rule_code = ?', [$r['code']]);
  $ruleSymptoms[$r['code']] = array_map(fn($x) => $x['symptom_code'], $rows);
}

// Forward chaining sederhana: jika semua gejala rule termasuk dalam gejala terpilih => match
$matches = [];
foreach ($rules as $r) {
  $need = $ruleSymptoms[$r['code']] ?? [];
  $missing = array_diff($need, $selected);
  if (count($missing) === 0 && count($need) > 0) {
    $matches[] = [
      'rule' => $r['code'],
      'disease' => $r['disease_code'],
      'need' => $need,
    ];
  }
}

// Jika tidak ada match penuh, tampilkan top 3 partial match berdasarkan coverage
$suggestions = [];
if (empty($matches)) {
  foreach ($rules as $r) {
    $need = $ruleSymptoms[$r['code']] ?? [];
    if (count($need) === 0) continue;
    $intersection = array_intersect($selected, $need);
    $coverage = count($intersection) / count($need);
    $missing = array_diff($need, $selected);
    $suggestions[] = [
      'rule' => $r['code'],
      'disease' => $r['disease_code'],
      'coverage' => $coverage,
      'have' => $intersection,
      'missing' => $missing,
    ];
  }
  usort($suggestions, fn($a,$b) => $b['coverage'] <=> $a['coverage']);
  $suggestions = array_slice($suggestions, 0, 3);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hasil Diagnosa – ISPA</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="container">
    <div class="header">
      <div class="title">Hasil Diagnosa</div>
      <a class="btn btn-primary" href="index.php">&larr; Kembali</a>
    </div>

    <div class="card result">
      <h3>Gejala yang dipilih (<?= count($selected) ?>)</h3>
      <div style="display:flex;flex-wrap:wrap;gap:6px;margin:6px 0">
        <?php foreach ($selected as $g): ?>
          <span class="badge"><?= htmlspecialchars($g) ?></span>
        <?php endforeach; ?>
      </div>
    </div>

    <?php if (!empty($matches)): ?>
    <div class="card result" style="margin-top:10px">
      <h3>Diagnosa Teridentifikasi</h3>
      <?php foreach ($matches as $m): ?>
        <p><strong><?= htmlspecialchars($diseases[$m['disease']] ?? $m['disease']) ?></strong> (<?= htmlspecialchars($m['disease']) ?>) – via aturan <code><?= htmlspecialchars($m['rule']) ?></code></p>
        <div class="muted">Gejala kaidah: <?= implode(', ', $m['need']) ?></div>
        <hr style="border-color:var(--border)">
      <?php endforeach; ?>
      <p>Catatan: Hasil ini adalah bantuan diagnosa dan bukan pengganti pemeriksaan dokter.</p>
    </div>
    <?php else: ?>
    <div class="card result" style="margin-top:10px">
      <h3>Tidak ada kecocokan penuh</h3>
      <p>Kemungkinan teratas berdasarkan kecocokan gejala:</p>
      <ol>
        <?php foreach ($suggestions as $s): ?>
          <li>
            <strong><?= htmlspecialchars($diseases[$s['disease']] ?? $s['disease']) ?></strong> (<?= htmlspecialchars($s['disease']) ?>)
            – coverage <?= number_format($s['coverage']*100, 0) ?>%
            <div class="muted">Cocok: <?= implode(', ', $s['have']) ?> | Kurang: <?= implode(', ', $s['missing']) ?></div>
          </li>
        <?php endforeach; ?>
      </ol>
      <p>Tambahkan gejala yang sesuai untuk meningkatkan akurasi.</p>
    </div>
    <?php endif; ?>
  </div>
</body>
</html>