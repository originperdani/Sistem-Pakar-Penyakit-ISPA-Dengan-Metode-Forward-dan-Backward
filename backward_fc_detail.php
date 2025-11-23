<?php
require __DIR__ . '/config.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function e($s) {
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

$id = isset($_GET['id']) ? $_GET['id'] : '';
if (empty($id)) {
    header('Location: backward_fc_list.php');
    exit;
}

$disease = getAll('SELECT code, name, description FROM diseases WHERE code = ?', [$id]);
if (!$disease) {
    header('Location: backward_fc_list.php');
    exit;
}
$disease = $disease[0];

// Backward chaining rules: disease code => list of symptom codes
$rules = [
    'P001' => ['G001', 'G002', 'G003', 'G007', 'G008', 'G011', 'G023', 'G027'],
    'P002' => ['G001', 'G002', 'G003', 'G004', 'G008', 'G009', 'G010', 'G013', 'G015', 'G017', 'G018', 'G019', 'G020'],
    'P003' => ['G001', 'G002', 'G007', 'G008', 'G010', 'G011', 'G012', 'G015', 'G030'],
    'P004' => ['G001', 'G002', 'G004', 'G008', 'G010', 'G012', 'G016', 'G023', 'G024', 'G025'],
    'P005' => ['G001', 'G002', 'G004', 'G005', 'G006', 'G007', 'G009', 'G012', 'G013', 'G025'],
    'P006' => ['G001', 'G004', 'G005', 'G006', 'G011', 'G013', 'G014'],
    'P007' => ['G001', 'G002', 'G008', 'G010', 'G016', 'G021', 'G022', 'G023', 'G026'],
    'P008' => ['G001', 'G002', 'G003', 'G004', 'G009', 'G013', 'G017', 'G027', 'G029'],
    'P009' => ['G001', 'G002', 'G003', 'G004', 'G005', 'G006', 'G007', 'G008', 'G009', 'G010', 'G011', 'G012', 'G013', 'G017', 'G022', 'G029'],
];

// Get all symptoms data once for name lookup
$allSymptomsList = getAll('SELECT code, name FROM symptoms');
$allSymptoms = [];
foreach ($allSymptomsList as $sym) {
    $allSymptoms[$sym['code']] = $sym['name'];
}

// Symptoms to ask for this disease according to rules
$currentDiseaseRules = $rules[$id] ?? [];

if (!$currentDiseaseRules) {
    // No rules for this disease, show message and exit
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head><meta charset="UTF-8" /><title>No Rules</title></head>
    <body><p>Tidak ditemukan aturan gejala untuk penyakit ini.</p></body>
    </html>
    <?php
    exit;
}

// Initialize session variable for answers
if (!isset($_SESSION['check_symptoms'])) {
    $_SESSION['check_symptoms'] = [];
}

// Get current question index from URL parameter or start at 0
$currentQuestion = isset($_GET['q']) ? intval($_GET['q']) : 0;

// Process submitted answer if any
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['answer'])) {
    $answer = $_POST['answer']; // expected values: 'iyaa' or 'tidak'

    // Save answer indexed by current question
    if ($currentQuestion >= 0 && $currentQuestion < count($currentDiseaseRules)) {
        $_SESSION['check_symptoms'][$currentQuestion] = $answer;
    }

    // Advance to next question
    $nextQuestion = $currentQuestion + 1;

    if ($nextQuestion >= count($currentDiseaseRules)) {
        // All questions answered, evaluate results

        // Check for perfect match (all symptoms answered "iyaa")
        $allSymptomsYes = true;
        foreach ($_SESSION['check_symptoms'] as $ans) {
            if ($ans !== 'iyaa') {
                $allSymptomsYes = false;
                break;
            }
        }

        // Prepare matched symptoms list for current disease
        $matchedSymptoms = [];
        foreach ($_SESSION['check_symptoms'] as $idx => $ans) {
            if ($ans === 'iyaa') {
                $symCode = $currentDiseaseRules[$idx];
                $matchedSymptoms[] = e($allSymptoms[$symCode] ?? $symCode);
            }
        }

        // Coverage results for all diseases
        $coverageResults = [];
        // Load disease names for display
        $allDiseasesList = getAll('SELECT code, name FROM diseases');
        $allDiseases = [];
        foreach ($allDiseasesList as $d) {
            $allDiseases[$d['code']] = $d['name'];
        }

        // Prepare user answers mapping symptom code => answer
        $userAnswers = [];
        foreach ($_SESSION['check_symptoms'] as $idx => $ans) {
            $symCode = $currentDiseaseRules[$idx];
            $userAnswers[$symCode] = $ans;
        }

        foreach ($rules as $dCode => $symptoms) {
            $matched = [];
            $missing = [];
            foreach ($symptoms as $symCode) {
                $ans = $userAnswers[$symCode] ?? 'tidak';
                if ($ans === 'iyaa') {
                    $matched[] = $symCode;
                } else {
                    $missing[] = $symCode;
                }
            }
            $total = count($symptoms);
            $coverage = $total > 0 ? count($matched) / $total : 0;
            $coverageResults[] = [
                'disease' => $dCode,
                'coverage' => $coverage,
                'matched' => $matched,
                'missing' => $missing,
            ];
        }

        // Sort coverage results by coverage descending
        usort($coverageResults, fn($a, $b) => $b['coverage'] <=> $a['coverage']);

        // Clear session just after evaluation
        unset($_SESSION['check_symptoms']);

        ?>
        <!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8"/>
            <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
            <title>Hasil Pengecekan Gejala – <?= e($disease['name']) ?></title>
            <link rel="stylesheet" href="style.css"/>
        </head>
        <body>
        <div class="container">
            <div class="header">
                <div class="title">Hasil Pengecekan – <?= e($disease['name']) ?></div>
                <div class="actions">
                    <a class="btn btn-primary" href="<?= base_path('/backward_fc_list.php') ?>">Kembali ke Daftar Penyakit</a>
                </div>
            </div>
            <div class="card">
                <?php if ($allSymptomsYes): ?>
                    <h3>Gejala yang Anda alami sesuai dengan penyakit <?= e($disease['name']) ?>.</h3>
                    <p>Gejala yang cocok:</p>
                    <ul>
                        <?php foreach ($matchedSymptoms as $symptom): ?>
                            <li><?= $symptom ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <p>Silakan lanjutkan diagnosa atau konsultasi lebih lanjut.</p>
                <?php else: ?>
                    <?php
                        // Disease with highest coverage
                        $topDisease = $coverageResults[0] ?? null;
                        $topDiseaseName = $topDisease ? ($allDiseases[$topDisease['disease']] ?? $topDisease['disease']) : '';
                        $topDiseaseCoverage = $topDisease ? number_format($topDisease['coverage'] * 100, 0) : '0';
                        $topDiseaseMatched = $topDisease ? array_map(fn($code) => e($allSymptoms[$code] ?? $code), $topDisease['matched']) : [];
                        $topDiseaseMissing = $topDisease ? array_map(fn($code) => e($allSymptoms[$code] ?? $code), $topDisease['missing']) : [];
                    ?>
                    <h3>Penyakit yang kamu idamkan adalah <strong><?= $topDiseaseName ?></strong> dengan tingkat kecocokan sebesar <strong><?= $topDiseaseCoverage ?>%</strong>.</h3>
                    <div style="margin: 1em 0; font-size: 1.1rem;">
                    <?php
                        if ($disease['code'] === ($topDisease['disease'] ?? '')) {
                            echo "Berdasarkan gejala yang kamu pilih, <strong>" . e($topDiseaseName) . "</strong> adalah penyakit yang paling sesuai dengan kondisimu.";
                        } else {
                            echo "Pemeriksaan gejala menunjukkan bahwa penyakit yang sedang diperiksa (<strong>" . e($disease['name']) . "</strong>) kurang cocok. Namun, kemungkinan besar penyebab gejala adalah <strong>" . e($topDiseaseName) . "</strong>.";
                        }
                    ?>
                    </div>
                    <h4>Detail kecocokan gejala:</h4>
                    <p><strong>Cocok:</strong> <?= implode(', ', $topDiseaseMatched) ?: 'Tidak ada' ?></p>
                    <p><strong>Kurang:</strong> <?= implode(', ', $topDiseaseMissing) ?: 'Tidak ada' ?></p>
                    <h4>Daftar kemungkinan penyakit lain berdasarkan kecocokan gejala:</h4>
                    <ol>
                        <?php foreach ($coverageResults as $res): ?>
                            <li>
                                <?= htmlspecialchars($allDiseases[$res['disease']] ?? $res['disease']) ?>
                                – coverage <?= number_format($res['coverage'] * 100, 0) ?>%
                                <br />
                                Cocok: <?php
                                    $matchedNames = array_map(fn($code) => e($allSymptoms[$code] ?? $code), $res['matched']);
                                    echo implode(', ', $matchedNames) ?: 'Tidak ada';
                                ?>
                                | Kurang: <?php
                                    $missingNames = array_map(fn($code) => e($allSymptoms[$code] ?? $code), $res['missing']);
                                    echo implode(', ', $missingNames) ?: 'Tidak ada';
                                ?>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                    <p>Silakan periksa penyakit lain atau konsultasikan dengan dokter untuk pemeriksaan lebih akurat.</p>
                <?php endif; ?>
            </div>
        </div>
        </body>
        </html>
        <?php
        exit;
    } else {
        // Redirect to next question
        header('Location: backward_fc_detail.php?id=' . urlencode($id) . '&q=' . $nextQuestion);
        exit;
    }
}

// Show current question
if ($currentQuestion < 0 || $currentQuestion >= count($currentDiseaseRules)) {
    // Invalid question index, redirect to first question
    header('Location: backward_fc_detail.php?id=' . urlencode($id) . '&q=0');
    exit;
}

$currentSymptomCode = $currentDiseaseRules[$currentQuestion];
$currentSymptomName = $allSymptoms[$currentSymptomCode] ?? $currentSymptomCode;

?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Pengecekan Gejala – <?= e($disease['name']) ?></title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <div class="container">
    <div class="header">
      <div class="title">Pengecekan Gejala – <?= e($disease['name']) ?></div>
      <div class="actions">
        <a class="btn btn-primary" href="<?= base_path('/backward_fc_list.php') ?>">Kembali</a>
      </div>
    </div>

    <div class="card" style="text-align:center; padding: 20px;">
      <h3>Apakah kamu mengalami gejala berikut?</h3>
      <p style="font-size: 1.2rem; font-weight: 600; margin: 20px 0;"><?= e($currentSymptomName) ?></p>

      <form method="post" action="backward_fc_detail.php?id=<?= urlencode($id) ?>&q=<?= $currentQuestion ?>">
        <button type="submit" name="answer" value="iyaa" class="btn btn-primary" style="margin-right: 10px; padding: 10px 30px;">iyaa</button>
        <button type="submit" name="answer" value="tidak" class="btn btn-secondary" style="padding: 10px 30px;">Tidak</button>
      </form>
    </div>
  </div>
</body>
</html>
