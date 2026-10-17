<?php
declare(strict_types=1);

require_once '../includes/user_auth.php';
require_once '../connections/connTraining.php';

$email = requireUser();
mysqli_select_db($connTraining, $database_connTraining);

$brandRoot     = '../';
$headerSection = 'Foundation training';

// First name
$stmt = $connTraining->prepare('SELECT first_name FROM training_user WHERE email_address = ?');
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->bind_result($firstName);
$stmt->fetch();
$stmt->close();

// Recorded tests, newest first
$tests = [];
$stmt  = $connTraining->prepare(
    'SELECT date_time, type, questions, correct, wrong, missed
     FROM training_tracker WHERE email_address = ? ORDER BY date_time DESC'
);
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->bind_result($dateTime, $type, $questions, $correct, $wrong, $missed);
while ($stmt->fetch()) {
    $tests[] = [
        'when'      => date('d-m-Y H:i:s', (int) strtotime((string) $dateTime)),
        'type'      => ['F' => 'Foundation', 'I' => 'Intermediate', 'A' => 'Full Level'][$type] ?? (string) $type,
        'questions' => (int) $questions,
        'correct'   => (int) $correct,
        'wrong'     => (int) $wrong,
        'missed'    => (int) $missed,
        'score'     => $questions > 0 ? $correct / $questions * 100 : 0,
    ];
}
$stmt->close();

$average = $tests ? array_sum(array_column($tests, 'score')) / count($tests) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Training Tracker</title>
<link rel="stylesheet" href="../includes/admin.css?v=<?= (int) @filemtime(__DIR__ . '/../includes/admin.css') ?>">
</head>
<body>

<?php require __DIR__ . '/../includes/admin_header.php'; ?>
<main class="page">

  <div class="panel panel--wide">
    <h1>Track training for <?= h($firstName) ?></h1>

    <?php if (!$tests): ?>
      <p class="count">You have no recorded tests.</p>
    <?php else: ?>
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>Date/Time</th><th>Type</th><th>Questions</th>
              <th>Correct</th><th>Wrong</th><th>Missed</th><th>Score (%)</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($tests as $t): ?>
              <tr>
                <td><?= h($t['when']) ?></td>
                <td><?= h($t['type']) ?></td>
                <td><?= $t['questions'] ?></td>
                <td><?= $t['correct'] ?></td>
                <td><?= $t['wrong'] ?></td>
                <td><?= $t['missed'] ?></td>
                <td><?= number_format($t['score'], 2) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
          <tfoot>
            <tr>
              <th colspan="6">Average</th>
              <th><?= number_format($average, 2) ?></th>
            </tr>
          </tfoot>
        </table>
      </div>
    <?php endif; ?>

    <div class="btn-row">
      <a class="btn btn-primary" href="main.php">Return</a>
    </div>
  </div>

</main>

<?php require __DIR__ . '/../includes/site_background.php'; ?>

</body>
</html>
