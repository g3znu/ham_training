<?php
declare(strict_types=1);

require_once '../includes/user_auth.php';
require_once '../includes/quiz_lib.php';
require_once '../connections/connTraining.php';

$email = requireUser();
mysqli_select_db($connTraining, $database_connTraining);

$q       = getInt('q', 1, 1, 100);
$locked  = testIsLocked();   // true once the learner has pressed Check
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

// Save an answer (sent automatically as soon as a choice is made; a plain form post also works)
if ($inTest && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $choice  = strtoupper(postString('choice'));
    $problem = '';

    if ($locked) {
        $problem = 'This test has already been checked, so your answers are locked.';
    } elseif (!csrfValid(postString('csrf_token'))) {
        $problem = 'Your session has expired. Please reload the page and try again.';
    } elseif (!in_array($choice, ['A', 'B', 'C', 'D'], true)) {
        $problem = 'Please choose an answer.';
    } else {
        $stmt = $connTraining->prepare(
            'UPDATE training_test SET given_answer = ? WHERE email_address = ? AND q_no = ?'
        );
        $stmt->bind_param('ssi', $choice, $email, $q);
        $stmt->execute();
        $stmt->close();

        $given   = $choice;
        $message = 'Answer saved. You can change it at any time before you press Check.';
    }

    if (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'fetch') {
        header('Content-Type: application/json');
        echo json_encode($problem === '' ? ['ok' => true] : ['ok' => false, 'error' => $problem]);
        exit;
    }

    $error = $problem;
}

if ($locked && $message === '' && $error === '') {
    $message = 'This test has been checked, so your answers are locked. '
             . 'Click "Back to Question selection" to start a new set.';
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
    $name = trim(str_replace('"', '', $name));
    // Letters, digits, spaces, "_" and "-" only: names such as "01 Cell" are fine, paths are not.
    $name = trim(preg_replace('/[^A-Za-z0-9 _-]/', '', $name));
    if ($name !== '') {
        $figures[] = 'images/' . rawurlencode($name) . '.jpg';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Question <?= $q ?></title>
<link rel="stylesheet" href="../includes/admin.css?v=<?= (int) @filemtime(__DIR__ . '/../includes/admin.css') ?>">
</head>
<body class="embed">

<?php if ($question === null): ?>

  <div class="panel panel--slim">
    <p class="intro">This question isn't part of your current test. Start a new set of questions from the menu.</p>
  </div>

<?php else: ?>

  <form class="panel panel--slim" id="answer-form" method="post" action="">
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

    <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">

    <ul class="options">
      <?php foreach ($question['options'] as $letter => $text): ?>
        <li>
          <label>
            <input type="radio" name="choice" value="<?= $letter ?>"
                   <?= $given === $letter ? 'checked' : '' ?>
                   <?= $locked ? 'disabled' : '' ?>>
            <span><strong><?= $letter ?>.</strong> <?= $text ?></span>
          </label>
        </li>
      <?php endforeach; ?>
    </ul>

    <div id="save-status" class="<?= $error !== '' ? 'error' : 'flash' ?>" role="status" aria-live="polite"><?= h($error !== '' ? $error : $message) ?></div>

    <?php if (!$locked): ?>
      <noscript>
        <div class="actions"><button type="submit" class="btn btn-primary">Save answer</button></div>
      </noscript>
    <?php endif; ?>
  </form>

  <script>
  (function () {
    var form = document.getElementById('answer-form');
    var status = document.getElementById('save-status');
    var q = <?= $q ?>;

    function show(kind, text) {
      status.className = kind;
      status.textContent = text;
    }

    // Save the answer as soon as a choice is made, then tell the question buttons to turn yellow.
    form.addEventListener('change', function (e) {
      if (e.target.name !== 'choice') { return; }
      show('flash', 'Saving\u2026');

      fetch(window.location.href, {
        method: 'POST',
        body: new FormData(form),
        credentials: 'same-origin',
        headers: { 'X-Requested-With': 'fetch' }
      })
        .then(function (r) { return r.json(); })
        .then(function (res) {
          if (res.ok) {
            show('flash', 'Answer saved. You can change it at any time before you press Check.');
            if (window.parent !== window) {
              window.parent.postMessage({ type: 'answered', q: q }, window.location.origin);
            }
          } else {
            show('error', res.error);
          }
        })
        .catch(function () {
          show('error', 'Your answer could not be saved. Please try again.');
        });
    });
  })();
  </script>

<?php endif; ?>

</body>
</html>
