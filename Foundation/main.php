<?php
declare(strict_types=1);

require_once '../includes/user_auth.php';

requireUser();

$brandRoot     = '../';
$headerSection = 'Foundation training';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Main Program</title>
<link rel="stylesheet" href="../includes/admin.css">
</head>
<body>

<?php require __DIR__ . '/../includes/admin_header.php'; ?>
<main class="page">

  <div class="panel panel--menu">
    <h1>Foundation Licence Questions</h1>
    <p class="lead">Please select a number of questions&hellip;</p>

    <a class="btn btn-block btn-primary" href="create_questions.php?n=5">5 Questions</a>
    <a class="btn btn-block btn-primary" href="create_questions.php?n=10">10 Questions</a>
    <a class="btn btn-block btn-primary" href="create_questions2.php">Mock Exam</a>
    <a class="btn btn-block" href="track_training.php">Track Training</a>
    <a class="btn btn-block" href="slides_or_exam.php">Back to Foundation main menu</a>
  </div>

</main>

<?php require __DIR__ . '/../includes/site_background.php'; ?>

</body>
</html>
