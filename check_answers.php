<?php
declare(strict_types=1);

require_once '../includes/user_auth.php';
require_once '../includes/quiz_lib.php';
require_once '../connections/connTraining.php';

$email  = requireUser();
$isMock = isset($_GET['mock']);
mysqli_select_db($connTraining, $database_connTraining);

function plural(int $n, string $one, string $many): string
{
    return $n === 1 ? $one : $many;
}

// First name
$stmt = $connTraining->prepare('SELECT first_name FROM training_user WHERE email_address = ?');
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->bind_result($firstName);
$stmt->fetch();
$stmt->close();

// This learner's current test
$rows = [];
$stmt = $connTraining->prepare(
    'SELECT q_no, question, answer, given_answer, section
     FROM training_test WHERE email_address = ? ORDER BY q_no'
);
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->bind_result($qNo, $qId, $answer, $given, $section);
while ($stmt->fetch()) {
    $rows[] = [
        'q_no'     => (int) $qNo,
        'question' => (int) $qId,
        'answer'   => strtoupper(trim((string) $answer)),
        'given'    => strtoupper(trim((string) $given)),
        'section'  => (int) $section,
    ];
}
$stmt->close();

// Mark it
$correct = $wrong = $missed = 0;
$missedNumbers = [];
$wrongRows     = [];
$sections      = [];

foreach ($rows as $r) {
    $s = $r['section'];
    $sections[$s]['total']   = ($sections[$s]['total'] ?? 0) + 1;
    $sections[$s]['correct'] = $sections[$s]['correct'] ?? 0;

    if ($r['given'] === '') {
        $missed++;
        $missedNumbers[] = $r['q_no'];
    } elseif ($r['given'] === $r['answer']) {
        $correct++;
        $sections[$s]['correct']++;
    } else {
        $wrong++;
        $wrongRows[] = $r;
    }
}

$total = count($rows);
$score = $total > 0 ? $correct / $total * 100 : 0;

// Record the result in the tracker (once per set of answers, so a refresh doesn't add duplicates)
if ($total > 0) {
    lockTest();   // answers can't be changed once the test has been checked

    $signature = md5(json_encode($rows));
    if (($_SESSION['tracked_signature'] ?? '') !== $signature) {
        $date = date('Y-m-d H:i:s');
        $type = 'F';
        $stmt = $connTraining->prepare(
            'INSERT INTO training_tracker (email_address, date_time, type, questions, correct, wrong, missed)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->bind_param('sssiiii', $email, $date, $type, $total, $correct, $wrong, $missed);
        $stmt->execute();
        $stmt->close();
        $_SESSION['tracked_signature'] = $signature;
    }
}

// Details of the questions answered wrongly
$details = [];
if ($wrongRows) {
    $stmt = $connTraining->prepare(
        'SELECT q_text, ans_a, ans_b, ans_c, ans_d, reference FROM training_foundation WHERE question = ?'
    );
    foreach ($wrongRows as $r) {
        $id = $r['question'];
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($qText, $ansA, $ansB, $ansC, $ansD, $reference);
        if ($stmt->fetch()) {
            $details[$r['q_no']] = [
                'text'      => $qText,
                'options'   => ['A' => $ansA, 'B' => $ansB, 'C' => $ansC, 'D' => $ansD],
                'reference' => $reference,
            ];
        }
        $stmt->free_result();
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Check answers</title>
<link rel="stylesheet" href="../includes/admin.css?v=<?= (int) @filemtime(__DIR__ . '/../includes/admin.css') ?>">
</head>
<body class="embed">

<div class="panel panel--quiz">

<?php if ($total === 0): ?>

  <p class="intro">There are no questions to check. Start a set of questions from the menu first.</p>

<?php else: ?>

  <h1>Your results</h1>

  <p class="score">You scored <?= number_format($score, 2) ?>%</p>
  <p class="fine">Your answers are now locked. Click "Back to Question selection" to start a new set.</p>
  <p class="intro">
    <?= h($firstName) ?>, you have <?= $correct ?> correct <?= plural($correct, 'answer', 'answers') ?>,
    <?= $wrong ?> wrong <?= plural($wrong, 'answer', 'answers') ?>
    and missed <?= $missed ?> <?= plural($missed, 'answer', 'answers') ?>.
    <?php if ($isMock && $score < 100): ?>
      The table below shows where you need to study further.
    <?php endif; ?>
  </p>

  <?php if ($isMock): ?>
    <div class="table-wrap">
      <table>
        <caption>Foundation Mock Exam Results</caption>
        <thead>
          <tr><th>Section</th><th>Questions</th><th>Correct</th><th>%</th></tr>
        </thead>
        <tbody>
          <?php foreach (mockSections() as $num => [$name, $count]): ?>
            <?php
              $sTotal   = $sections[$num]['total'] ?? 0;
              $sCorrect = $sections[$num]['correct'] ?? 0;
            ?>
            <tr>
              <td><?= h($name) ?></td>
              <td><?= $sTotal ?></td>
              <td><?= $sCorrect ?></td>
              <td><?= $sTotal > 0 ? number_format($sCorrect / $sTotal * 100, 2) : '0.00' ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>

  <?php if ($missed > 0): ?>
    <p><strong>Missed questions:</strong> <?= h(implode(', ', $missedNumbers)) ?></p>
  <?php endif; ?>

  <?php foreach ($wrongRows as $r): ?>
    <?php if (!isset($details[$r['q_no']])): continue; endif; ?>
    <?php $d = $details[$r['q_no']]; ?>
    <div class="review-card">
      <p class="question">Question <?= $r['q_no'] ?></p>
      <p><?= $d['text'] ?></p>
      <ul class="options review">
        <?php foreach ($d['options'] as $letter => $text): ?>
          <li class="<?= $letter === $r['answer'] ? 'correct' : ($letter === $r['given'] ? 'wrong' : '') ?>">
            <label>
              <span>
                <strong><?= $letter ?>.</strong> <?= $text ?>
                <?php if ($letter === $r['answer']): ?> <em>(correct answer)</em><?php endif; ?>
                <?php if ($letter === $r['given']): ?> <em>(your answer)</em><?php endif; ?>
              </span>
            </label>
          </li>
        <?php endforeach; ?>
      </ul>
      <?php if (trim((string) $d['reference']) !== ''): ?>
        <p><strong>Please refer to:</strong></p>
        <div class="reference"><?= h($d['reference']) ?></div>
      <?php endif; ?>
    </div>
  <?php endforeach; ?>

<?php endif; ?>

  <div class="btn-row">
    <button type="button" class="btn btn-primary" onclick="window.close()">Close</button>
  </div>
</div>

</body>
</html>
