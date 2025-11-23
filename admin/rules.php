<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
echo "Debug: rules.php berhasil dijalankan.<br>";
require_once __DIR__ . '/../config.php';
require_admin();
$db = db();

// Delete rule
if (isset($_GET['del'])) {
  $code = $_GET['del'];
  $db->prepare('DELETE FROM rule_symptoms WHERE rule_code = ?')->execute([$code]);
  $db->prepare('DELETE FROM rules WHERE code = ?')->execute([$code]);
  header('Location: rules.php');
  exit;
}

// Create/Update rule
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'save_rule') {
  $code = trim($_POST['code'] ?? '');
  $disease = trim($_POST['disease_code'] ?? '');
  if ($code && $disease) {
    $exists = $db->prepare('SELECT COUNT(*) FROM rules WHERE code = ?');
    $exists->execute([$code]);
    if ($exists->fetchColumn() > 0) {
      $db->prepare('UPDATE rules SET disease_code = ? WHERE code = ?')->execute([$disease, $code]);
    } else {
      $db->prepare('INSERT INTO rules (code, disease_code) VALUES (?, ?)')->execute([$code, $disease]);
    }
  }
  header('Location: rules.php?edit=' . urlencode($code));
  exit;
}

// Save mapping symptoms
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'save_mapping') {
  $rule = trim($_POST['rule_code'] ?? '');
  $symptoms = $_POST['symptoms'] ?? [];
  if ($rule) {
    $db->prepare('DELETE FROM rule_symptoms WHERE rule_code = ?')->execute([$rule]);
    foreach ($symptoms as $s) {
      $db->prepare('INSERT INTO rule_symptoms (rule_code, symptom_code) VALUES (?, ?)')->execute([$rule, $s]);
    }
  }
  header('Location: rules.php?edit=' . urlencode($rule));
  exit;
}

$editCode = $_GET['edit'] ?? null;
$editRow = null;
$editSymptoms = [];
if ($editCode) {
  $stmt = $db->prepare('SELECT * FROM rules WHERE code = ?');
  $stmt->execute([$editCode]);
  $editRow = $stmt->fetch(PDO::FETCH_ASSOC);
  $editSymptoms = getAll('SELECT symptom_code FROM rule_symptoms WHERE rule_code = ?', [$editCode]);
  $editSymptoms = array_map(fn($r) => $r['symptom_code'], $editSymptoms);
}

$diseases = getAll('SELECT code, name FROM diseases ORDER BY code');
$symptoms = getAll('SELECT code, name FROM symptoms ORDER BY code');
$rules = getAll('SELECT r.code, d.code AS disease_code, d.name AS disease_name FROM rules r LEFT JOIN diseases d ON d.code = r.disease_code ORDER BY r.code');
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Kelola Aturan • ISPA Admin</title>
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
      <a href="diseases.php">Penyakit</a>
      <a href="symptoms.php">Gejala</a>
      <a class="active" href="rules.php">Aturan</a>
      <a href="../index.php">Diagnosa</a>
      <a href="logout.php">Keluar</a>
    </nav>
  </header>

  <main class="container">
    <div class="card" style="margin-bottom:16px">
      <div class="section-header">
        <div class="title"><?php echo $editRow ? 'Edit Aturan' : 'Tambah Aturan'; ?></div>
        <a class="btn" href="rules.php">Batal</a>
      </div>
      <form method="post" class="form-grid">
        <input type="hidden" name="action" value="save_rule" />
        <div>
          <label>Kode Aturan</label>
          <input class="input" name="code" value="<?php echo htmlspecialchars($editRow['code'] ?? '') ?>" placeholder="R10" required />
        </div>
        <div>
          <label>Penyakit Terkait</label>
          <select class="select" name="disease_code" required>
            <option value="">Pilih penyakit...</option>
            <?php foreach ($diseases as $d): ?>
              <option value="<?php echo htmlspecialchars($d['code']); ?>" <?php echo (($editRow['disease_code'] ?? '') === $d['code']) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($d['code'] . ' — ' . $d['name']); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <button class="btn btn-primary" type="submit">Simpan Aturan</button>
        </div>
      </form>
    </div>

    <?php if ($editRow): ?>
    <div class="card" style="margin-bottom:16px">
      <div class="section-header">
        <div class="title">Mapping Gejala untuk: <?php echo htmlspecialchars($editRow['code']); ?></div>
        <div class="badge">Checklist Gejala</div>
      </div>
      <form method="post">
        <input type="hidden" name="action" value="save_mapping" />
        <input type="hidden" name="rule_code" value="<?php echo htmlspecialchars($editRow['code']); ?>" />
        <div class="grid">
          <?php foreach ($symptoms as $s): ?>
            <label class="symptom">
              <input type="checkbox" name="symptoms[]" value="<?php echo htmlspecialchars($s['code']); ?>" <?php echo in_array($s['code'], $editSymptoms) ? 'checked' : ''; ?> />
              <span><?php echo htmlspecialchars($s['code'] . ' — ' . $s['name']); ?></span>
            </label>
          <?php endforeach; ?>
        </div>
        <div style="margin-top:12px">
          <button class="btn btn-primary" type="submit">Simpan Mapping</button>
        </div>
      </form>
    </div>
    <?php endif; ?>

    <div class="card">
      <div class="section-header">
        <div class="title">Daftar Aturan</div>
        <a class="btn btn-primary" href="rules.php">Tambah Baru</a>
      </div>
      <div style="overflow:auto">
        <table class="table">
          <thead>
            <tr>
              <th>Kode</th>
              <th>Penyakit</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($rules as $r): ?>
              <tr>
                <td><?php echo htmlspecialchars($r['code']); ?></td>
                <td><?php echo htmlspecialchars(($r['disease_code'] ?: '-') . ' — ' . ($r['disease_name'] ?: '-')); ?></td>
                <td class="table-actions">
                  <a class="btn" href="?edit=<?php echo urlencode($r['code']); ?>">Edit</a>
                  <a class="btn" href="?del=<?php echo urlencode($r['code']); ?>" onclick="return confirm('Hapus aturan ini? Mapping gejala juga akan dihapus.')">Hapus</a>
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
    <div class="badge">Manajemen Aturan</div>
  </footer>
</body>
</html>
