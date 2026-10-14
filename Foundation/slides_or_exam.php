<?php
declare(strict_types=1);

require_once '../includes/user_auth.php';
require_once '../connections/connTraining.php';

$email = requireUser();
mysqli_select_db($connTraining, $database_connTraining);
touchLastAccessed($connTraining, $email);

$brandRoot     = '../';
$headerSection = 'Foundation training';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Foundation Licence Revision</title>
<link rel="stylesheet" href="../includes/admin.css">
</head>
<body>

<?php require __DIR__ . '/../includes/admin_header.php'; ?>
<main class="page">

  <div class="panel panel--slim">
    <h1>Foundation Licence Revision</h1>

    <p class="intro">
       The Foundation licence is the starting point for many people on their journey into amateur radio.
The Foundation Exam is administered by the Radio Society of Great Britain (RSGB)<br>
These revision questions are based on the latest syllabus and will help you to check your progress.
    </p>

    <a class="btn btn-block btn-primary" href="show_slides.php">1. Tutorial slides</a>
    <p class="fine">My thanks to <a href="https://www.essexham.co.uk" rel="noopener">essexham.co.uk</a> for the use of their tutorial slides.</p>

    <a class="btn btn-block btn-primary" href="exams.php">2. Exam questions</a>
    <p class="fine">My thanks to <a href="https://www.hamtests.co.uk" rel="noopener">hamtests.co.uk</a> for the use of their question bank.</p>

    <a class="btn btn-block" href="../index.php">3. Return to main menu</a>
  </div>

</main>

<?php require __DIR__ . '/../includes/site_background.php'; ?>

</body>
</html>
