<?php
declare(strict_types=1);

require_once '../includes/user_auth.php';
require_once '../connections/connTraining.php';

$email = requireUser();
mysqli_select_db($connTraining, $database_connTraining);

$q       = getInt('q', 1, 1, 100);
$message = '';
$error   = '';

// Which question is number $q in this learner's current test, and what have they answered so far?
$stmt = $connTraining->prepare(
    'SELECT question, given_answer FROM training_test WHERE email_address = ? AND q_no = ?'
);
$stmt->bind_param('si', $email, $q);
$stmt->execute();
$stmt->bind_result($questionId, $given);
$inTest = (bool) $stmt->fetch();
$stmt->close();
$given = strtoupper(trim((string) $given));

// Save an answer
if ($inTest && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $choice = strtoupper(postString('choice'));

    if (!csrfValid(postString('csrf_token'))) {
        $error = 'Your session has expired. Please reload the page and try again.';
    } elseif (!in_array($choice, ['A', 'B', 'C', 'D'], true)) {
        $error = 'Please choose an answer.';
    } else {
        $stmt = $connTraining->prepare(
            'UPDATE training_test SET given_answer = ? WHERE email_address = ? AND q_no = ?'
        );
        $stmt->bind_param('ssi', $choice, $email, $q);
        $stmt->execute();
        $stmt->close();

        $given   = $choice;
        $message = 'Answer saved. Choose another question, or press Check when you have finished.';
    }
}

// The question itself
$question = null;
if ($inTest) {
    $stmt = $connTraining->prepare(
        'SELECT q_text, ans_a, ans_b, ans_c, ans_d, image FROM training_foundation WHERE question = ?'
    );
    $stmt->bind_param('i', $questionId);
    $stmt->execute();
    $stmt->bind_result($qText, $ansA, $ansB, $ansC, $ansD, $image);
    if ($stmt->fetch()) {
        $question = ['text' => $qText, 'options' => ['A' => $ansA, 'B' => $ansB, 'C' => $ansC, 'D' => $ansD]];
    }
    $stmt->close();
}

// Figures: the image column holds comma-separated names such as "fig1","fig2"
$figures = [];
foreach (explode(',', (string) ($image ?? '')) as $name) {
    $name = preg_replace('/[^A-Za-z0-9_-]/', '', $name);
    if ($name !== '') {
        $figures[] = 'images/' . $name . '.jpg';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Question <?= $q ?></title>
<link rel="stylesheet" href="../includes/admin.css">
</head>
<body class="embed">

<?php if ($question === null): ?>

  <div class="panel panel--slim">
    <p class="intro">This question isn't part of your current test. Start a new set of questions from the menu.</p>
  </div>

<?php else: ?>

  <form class="panel panel--slim" method="post" action="">
    <?php if ($figures): ?>
      <div class="figures">
        <?php foreach ($figures as $src): ?>
          <img src="<?= h($src) ?>" alt="Diagram for question <?= $q ?>">
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <p class="question">Question <?= $q ?></p>
    <?php /* Question text comes from your own question bank, so it is output as stored. */ ?>
    <p><?= $question['text'] ?></p>

    <?php if ($error !== ''): ?>
      <div class="error" role="alert"><?= h($error) ?></div>
    <?php elseif ($message !== ''): ?>
      <div class="flash" role="status"><?= h($message) ?></div>
    <?php endif; ?>

    <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">

    <ul class="options">
      <?php foreach ($question['options'] as $letter => $text): ?>
        <li>
          <label>
            <input type="radio" name="choice" value="<?= $letter ?>"
                   <?= $given === $letter ? 'checked' : '' ?> <?= $letter === 'A' ? 'required' : '' ?>>
            <span><strong><?= $letter ?>.</strong> <?= $text ?></span>
          </label>
        </li>
      <?php endforeach; ?>
    </ul>

    <div class="actions">
      <button type="submit" class="btn btn-primary">Validate</button>
    </div>
  </form>

<?php endif; ?>

</body>
</html>
