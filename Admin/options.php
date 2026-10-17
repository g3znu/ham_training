<?php
declare(strict_types=1);

require_once '../includes/admin_auth.php';
requireAdmin();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// One-off message set by other admin pages (e.g. after registering a user).
$flash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);

$menu = [
    'register.php'   => 'Register User',
    'deregister.php' => 'Remove User',
    'showusers.php'  => 'Show Users',
    '../index.php'   => 'Users Menu',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin Index</title>
<link rel="stylesheet" href="../includes/admin.css?v=<?= (int) @filemtime(__DIR__ . '/../includes/admin.css') ?>">
</head>
<body>

<?php require __DIR__ . '/../includes/admin_header.php'; ?>
<main class="page">

<div class="panel panel--menu">
  <h1>Admin</h1>

  <?php if ($flash !== ''): ?>
    <div class="flash" role="status"><?= h($flash) ?></div>
  <?php endif; ?>

  <?php foreach ($menu as $url => $label): ?>
    <a href="<?= h($url) ?>" class="btn btn-block"><?= h($label) ?></a>
  <?php endforeach; ?>

  <form method="post" action="logout.php">
    <input type="hidden" name="csrf_token" value="<?= h($_SESSION['csrf_token']) ?>">
    <button type="submit" class="btn btn-block">Log out</button>
  </form>
</div>

</main>

<!-- Background slideshow: the image link is read by the script below -->
<div id="dwuserEasyBigBackground"
     style="position:fixed;left:0;right:0;top:0;bottom:0;z-index:-999;padding:0;margin:0;"
     data-ebbConfig="{opacity:0.7, delay:6000, loop:true, scaleMode:'fillArea', fadeInThreshold:3000, duration:1000, minScreenSize:481}"><a href="../Foundation/images/backdrops/02.jpg" rel="nofollow">&nbsp;</a></div>
<script src="../includes/easybigbackground.js"></script>

</body>
</html>
