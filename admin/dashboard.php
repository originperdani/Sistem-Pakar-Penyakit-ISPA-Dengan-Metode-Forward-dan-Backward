<?php
require_once __DIR__ . '/../config.php';
require_admin();

$db = db();
$diseasesCount = (int)$db->query('SELECT COUNT(*) FROM diseases')->fetchColumn();
$symptomsCount = (int)$db->query('SELECT COUNT(*) FROM symptoms')->fetchColumn();
$rulesCount    = (int)$db->query('SELECT COUNT(*) FROM rules')->fetchColumn();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dashboard ISPA - Admin</title>
  <link rel="stylesheet" href="../style.css" />
</head>
<body>
  <header class="app-header">
    <div class="brand">
      <div class="logo-dot"></div>
      <div class="brand-name">ISPA Clinic • Admin</div>
    </div>
    <nav class="app-nav">
      <a class="active" href="dashboard.php">Dashboard</a>
      <a href="diseases.php">Penyakit</a>
      <a href="symptoms.php">Gejala</a>
      <a href="../index.php">Diagnosa</a>
      <a href="logout.php">Keluar</a>
    </nav>
  </header>

  <main class="container">
    <div class="header">
      <div class="title">Ringkasan Sistem</div>
      <div class="pill">
        <span>Status:</span>
        <span class="badge">Aktif</span>
      </div>
    </div>

    <section class="dashboard-grid">
      <div class="card stat-card">
        <div class="stat-icon">🫁</div>
        <div>
          <div class="stat-value"><?php echo $diseasesCount; ?></div>
          <div class="stat-label">Jenis Penyakit</div>
        </div>
      </div>
      <div class="card stat-card">
        <div class="stat-icon">🧪</div>
        <div>
          <div class="stat-value"><?php echo $symptomsCount; ?></div>
          <div class="stat-label">Gejala</div>
        </div>
      </div>
    </section>

    <section class="card" style="margin-top:16px">
      <div class="section-header">
        <div class="title">Aksi Cepat</div>
        <div class="badge">Manajemen Data</div>
      </div>
      <div style="display:flex;gap:10px;flex-wrap:wrap">
        <a class="btn btn-primary" href="diseases.php">Kelola Penyakit</a>
        <a class="btn" href="symptoms.php">Kelola Gejala</a>
        <a class="btn" href="../index.php">Mulai Diagnosa</a>
      </div>
    </section>
  </main>

  <footer class="container footer">
    <div>© ISPA Clinic</div>
    <div class="badge">Forward Chaining</div>
  </footer>
</body>
</html>
