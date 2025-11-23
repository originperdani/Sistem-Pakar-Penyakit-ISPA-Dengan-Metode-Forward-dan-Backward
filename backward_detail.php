<?php
require __DIR__ . '/config.php';
function e($s){return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header('Location: backward_list.php'); exit; }

$hyp = getAll('SELECT id, code, name, description FROM backward_hypotheses WHERE id = ?', [$id]);
if (!$hyp) { header('Location: backward_list.php'); exit; }
$hyp = $hyp[0];

$rules = getAll(
  'SELECT s.code AS symptom_code, s.name AS symptom_name, q.question_text, r.required
   FROM backward_rules r
   JOIN backward_symptoms s ON s.id = r.symptom_id
   LEFT JOIN backward_questions q ON q.symptom_id = s.id
   WHERE r.hypothesis_id = ?
   ORDER BY r.id', [$id]
);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Backward Chaining – Detail <?= e($hyp['name']) ?></title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="container">
    <div class="header">
      <div class="title">Backward Chaining</div>
      <div class="actions">
        <a class="btn" href="backward_list.php">&larr; Kembali</a>
        <a class="btn btn-primary" href="index.php">Forward</a>
      </div>
    </div>

    <div class="card">
      <h2 class="mt-0"><?= e($hyp['name']) ?> <span class="badge">Kode: <?= e($hyp['code']) ?></span></h2>
      <?php if (!empty($hyp['description'])): ?><p class="muted"><?= e($hyp['description']) ?></p><?php endif; ?>

      <h3 class="mt-16">Daftar Gejala (Aturan)</h3>
      <?php if (empty($rules)): ?>
        <p class="muted">Belum ada aturan/gejala terkait untuk penyakit ini.</p>
      <?php else: ?>
        <table class="table">
          <thead>
            <tr>
              <th style="width:130px;">Kode Gejala</th>
              <th>Nama Gejala</th>
              <th>Pertanyaan</th>
              <th style="width:120px;">Sifat</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($rules as $r): ?>
              <tr>
                <td><strong><?= e($r['symptom_code']) ?></strong></td>
                <td><?= e($r['symptom_name']) ?></td>
                <td class="muted"><?= !empty($r['question_text']) ? e($r['question_text']) : '-' ?></td>
                <td><?= ((int)$r['required'] === 1) ? 'Diperlukan' : 'Opsional' ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
