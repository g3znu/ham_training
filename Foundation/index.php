<?php
declare(strict_types=1);

require_once '../includes/user_auth.php';
require_once '../connections/connTraining.php';

startAppSession();
mysqli_select_db($connTraining, $database_connTraining);

$brandRoot     = '../';
$headerSection = 'Foundation training';
$error         = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrfValid(postString('csrf_token'))) {
        $error = 'Your session has expired. Please try again.';
    } else {
        $email = postString('email_address');

        if ($email !== '' && loginUser($connTraining, $email)) {
            header('Location: slides_or_exam.php');
            exit;
        }

        // Not a registered address: explain how to register.
        header('Location: register.php');
        exit;
    }
}
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

  <form class="panel panel--slim" method="post" action="">
    <h1>Foundation Licence Revision</h1>

    <p class="intro">
      The Foundation licence is the starting point for many people on their journey into amateur radio.
The Foundation Exam is administered by the Radio Society of Great Britain (RSGB)<br>
These revision questions are based on the latest syllabus and will help you to check your progress.
    </p>

    <?php if ($error !== ''): ?>
      <div class="error" role="alert"><?= h($error) ?></div>
    <?php endif; ?>

    <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">

    <label for="email_address">Email address</label>
    <input type="email" id="email_address" name="email_address" maxlength="64"
           autocomplete="email" required autofocus>

    <div class="actions">
      <button type="submit" class="btn btn-primary">Log in</button>
    </div>
  </form>

</main>

<?php require __DIR__ . '/../includes/site_background.php'; ?>

</body>
</html>
