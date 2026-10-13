<?php
declare(strict_types=1);

require_once '../includes/user_auth.php';
require_once '../includes/quiz_lib.php';
require_once '../connections/connTraining.php';

$email = requireUser();
mysqli_select_db($connTraining, $database_connTraining);

$wanted = getInt('n', 5, 1, 30);
$picked = pickRandom(allQuestionIds($connTraining), $wanted);
saveTest($connTraining, $email, $picked);

$brandRoot     = '../';
$headerSection = 'Foundation training';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Create Questions</title>
<link rel="stylesheet" href="../includes/admin.css">
</head>
<body>

<?php require __DIR__ . '/../includes/admin_header.php'; ?>
<main class="page">

  <div class="panel panel--quiz">
    <h1>Your Questions</h1>

    <p class="intro">
      Click on each question button, select an answer and click the "Validate" button.
      When you're done, click the "Check" button to have your answers checked.
      Click "More Q's" to return to the questions menu.
    </p>

    <?php renderTestUi(count($picked), 'check_answers.php'); ?>
  </div>

</main>

<?php require __DIR__ . '/../includes/site_background.php'; ?>

</body>
</html>
