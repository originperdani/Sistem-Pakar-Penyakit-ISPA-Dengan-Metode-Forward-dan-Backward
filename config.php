<?php
session_start();
// Konfigurasi koneksi database MySQL (sesuaikan dengan XAMPP/Laragon kamu)
define('DB_HOST', 'localhost');
define('DB_NAME', 'ispa_db');
define('DB_USER', 'root');
define('DB_PASS', '');

function db() {
  static $pdo = null;
  if ($pdo) return $pdo;
  $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
  try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
  } catch (Exception $e) {
    http_response_code(500);
    echo '<h2>Gagal konek database</h2><p>' . htmlspecialchars($e->getMessage()) . '</p>';
    exit;
  }
  return $pdo;
}

function getAll($sql, $params = []) {
  $stmt = db()->prepare($sql);
  $stmt->execute($params);
  return $stmt->fetchAll();
}

function getMapBy($sql, $key, $val, $params = []) {
  $rows = getAll($sql, $params);
  $map = [];
  foreach ($rows as $r) { $map[$r[$key]] = $r[$val]; }
  return $map;
}

// Auth admin sederhana
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', 'admin123');

function is_admin() {
  return !empty($_SESSION['admin']);
}

function require_admin() {
  if (!is_admin()) {
    header('Location: ' . base_path('/admin/login.php'));
    exit;
  }
}

function base_path($path) {
  $root = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
  $base = $root === '' ? '' : $root;
  return $base . $path;
}

?>