<?php
declare(strict_types=1);

// Information page only: explains how to get registered. No login needed.
$brandRoot     = '../';
$headerSection = 'Foundation training';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Training Registration</title>
<link rel="stylesheet" href="../includes/admin.css">
</head>
<body>

<?php require __DIR__ . '/../includes/admin_header.php'; ?>
<main class="page">

  <div class="panel panel--slim">
    <h1>Foundation Licence Revision Guide</h1>
    <p class="intro"><strong>Slides &amp; Questions</strong></p>

    <p>
      To use this facility, please ask to be registered using the
      <strong>About Us &rarr; Contact Us</strong> options on the club's main menu.
    </p>
    <p>
      We need to know your first name, last name and email address. The email address is
      what you use to log in, and it lets you track your training. Thank you.
    </p>

    <div class="actions">
      <a class="btn btn-primary" href="../index.php">Back</a>
    </div>
  </div>

</main>

<?php require __DIR__ . '/../includes/site_background.php'; ?>

</body>
</html>
