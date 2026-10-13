<?php
declare(strict_types=1);

// This page sits in the app root, so the shared header needs these two settings.
$brandRoot     = '';
$headerSection = 'Licence training';

$courses = [
    'Foundation/index.php'   => 'Foundation Training',
    'Intermediate/index.php' => 'Intermediate Training',
    'Advanced/index.php'     => 'Advanced Training',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Licence Training – Chesham &amp; District Amateur Radio Society</title>
<link rel="stylesheet" href="includes/admin.css">
</head>
<body>

<?php require __DIR__ . '/includes/admin_header.php'; ?>
<main class="page">

  <div class="panel panel--menu">
    <h1>Where to?</h1>
    <p class="lead">Please select the required licence training.</p>

    <?php foreach ($courses as $url => $label): ?>
      <a href="<?= htmlspecialchars($url, ENT_QUOTES, 'UTF-8') ?>" class="btn btn-block"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></a>
    <?php endforeach; ?>
  </div>

</main>

<!-- Background slideshow: the image link is read by the script below -->
<div id="dwuserEasyBigBackground"
     style="position:fixed;left:0;right:0;top:0;bottom:0;z-index:-999;padding:0;margin:0;"
     data-ebbConfig="{opacity:0.7, delay:6000, loop:true, scaleMode:'fillArea', fadeInThreshold:3000, duration:1000, minScreenSize:481}"><a href="Foundation/images/backdrops/02.jpg" rel="nofollow">&nbsp;</a></div>
<script src="includes/easybigbackground.js"></script>

</body>
</html>
