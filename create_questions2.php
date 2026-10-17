<?php
declare(strict_types=1);

require_once '../includes/user_auth.php';
require_once '../includes/quiz_lib.php';
require_once '../connections/connTraining.php';

$email = requireUser();
mysqli_select_db($connTraining, $database_connTraining);

// Pick the right number of random questions from each section, in section order.
$picked = [];
foreach (mockSections() as $section => [$name, $count]) {
    $picked = array_merge($picked, pickRandom(allQuestionIds($connTraining, $section), $count));
}
saveTest($connTraining, $email, $picked);

$brandRoot     = '../';
$headerSection = 'Foundation training';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Mock Exam</title>
<link rel="stylesheet" href="../includes/admin.css?v=<?= (int) @filemtime(__DIR__ . '/../includes/admin.css') ?>">
</head>
<body>

<?php require __DIR__ . '/../includes/admin_header.php'; ?>
<main class="page">

  <div class="panel panel--quiz">
    <h1>Mock Exam</h1>

    <p class="intro">As this is a mock exam you will have the following number of questions:</p>

    <div class="table-wrap">
      <table class="compact">
        <tbody>
          <?php foreach (mockSections() as [$name, $count]): ?>
            <tr><td><?= h($name) ?></td><td><?= $count ?></td></tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <p class="intro">
      Click on each question button to see the question, then select your answer.
      Your answer is saved as soon as you choose it, and the question number turns yellow.
      You can go back to any question and change your answer.
      When you're done, click the "Check" button: your answers are then locked and your results shown.
      Click "Back to Question selection" to return to the questions menu.
    </p>

    <?php renderTestUi(count($picked), 'check_answers.php?mock=1', true); ?>
  </div>

</main>

<?php require __DIR__ . '/../includes/site_background.php'; ?>

</body>
</html>
