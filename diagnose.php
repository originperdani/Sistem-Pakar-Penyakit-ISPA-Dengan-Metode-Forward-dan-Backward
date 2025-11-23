<?php
require __DIR__ . '/config.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['symptoms'])) {
    header('Location: index.php');
    exit;
}
$selected = $_POST['symptoms'] ?? [];
if (!is_array($selected)) $selected = [];
$selected = array_values(array_unique($selected));

// Redirect to symptom selection page if no symptoms selected
if (empty($selected)) {
    header('Location: index.php');
    exit;
}

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

// Save results to session temporarily for display in result page
$_SESSION['diagnose_result'] = [
    'selected' => $selected,
    'matches' => $matches,
    'suggestions' => $suggestions,
    'diseases' => $diseases,
];

// Redirect to result page to prevent form resubmission
header('Location: diagnose_result.php');
exit;
?>
