<?php
declare(strict_types=1);

require_once '../includes/admin_auth.php';
require_once '../connections/connTraining.php';

const ADMIN_URL = 'options.php';   // main Admin page

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

/* Only a logged-in admin may remove users. */
requireAdmin();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function postString(string $key): string
{
    return is_string($_POST[$key] ?? null) ? trim($_POST[$key]) : '';
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = postString('email_address');
    $token = postString('csrf_token');

    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        $error = 'Your session has expired. Please try again.';
    } elseif ($email === '') {
        $error = 'Please enter an email address.';
    } else {
        try {
            mysqli_select_db($connTraining, $database_connTraining);
            $stmt = $connTraining->prepare('DELETE FROM training_user WHERE email_address = ?');
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $removed = $stmt->affected_rows;
            $stmt->close();

            if ($removed > 0) {
                // The admin session is left untouched, so they stay logged in.
                $_SESSION['flash'] = 'User removed: ' . $email;
                header('Location: ' . ADMIN_URL);
                exit;
            }

            $error = 'No registered user was found with that email address.';
        } catch (mysqli_sql_exception $e) {
            error_log('De-registration failed: ' . $e->getMessage());
            $error = 'Sorry, the user could not be removed. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Training De-Registration</title>
<link rel="stylesheet" href="../includes/admin.css">
</head>
<body>

<?php require __DIR__ . '/../includes/admin_header.php'; ?>
<main class="page">

<form class="panel" method="post" action=""
      onsubmit="return confirm('Remove this user? This cannot be undone.');">
  <h1>Remove User</h1>

  <?php if ($error !== ''): ?>
    <div class="error" role="alert"><?= h($error) ?></div>
  <?php endif; ?>

  <input type="hidden" name="csrf_token" value="<?= h($_SESSION['csrf_token']) ?>">

  <label for="email_address">Email address</label>
  <input type="email" id="email_address" name="email_address" maxlength="64"
         value="<?= h(postString('email_address')) ?>" autocomplete="off" required autofocus>

  <div class="actions">
    <button type="submit" class="btn btn-primary">Remove</button>
    <a href="<?= h(ADMIN_URL) ?>" class="btn">Cancel</a>
  </div>
</form>

</main>

<!-- Background slideshow: the image link is read by the script below -->
<div id="dwuserEasyBigBackground"
     style="position:fixed;left:0;right:0;top:0;bottom:0;z-index:-999;padding:0;margin:0;"
     data-ebbConfig="{opacity:0.7, delay:6000, loop:true, scaleMode:'fillArea', fadeInThreshold:3000, duration:1000, minScreenSize:481}"><a href="../Foundation/images/backdrops/02.jpg" rel="nofollow">&nbsp;</a></div>
<script src="../includes/easybigbackground.js"></script>

</body>
</html>
