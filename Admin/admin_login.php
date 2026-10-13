<?php
declare(strict_types=1);

require_once '../connections/connTraining.php';

const LOGIN_SUCCESS_URL  = 'options.php';
const MAX_FAILED_ATTEMPTS = 5;   // per IP address
const LOCKOUT_MINUTES     = 15;

/* ---------- Session (cookie flags must be set before session_start) ---------- */

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => !empty($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/* ---------- Helpers ---------- */

function h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function isLockedOut(mysqli $db, string $ip): bool
{
    $stmt = $db->prepare(
        'SELECT COUNT(*) FROM login_attempts
         WHERE ip = ? AND attempted_at > (NOW() - INTERVAL ' . LOCKOUT_MINUTES . ' MINUTE)'
    );
    $stmt->bind_param('s', $ip);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    return $count >= MAX_FAILED_ATTEMPTS;
}

function recordFailure(mysqli $db, string $ip): void
{
    $stmt = $db->prepare('INSERT INTO login_attempts (ip, attempted_at) VALUES (?, NOW())');
    $stmt->bind_param('s', $ip);
    $stmt->execute();
    $stmt->close();
}

function clearFailures(mysqli $db, string $ip): void
{
    $stmt = $db->prepare('DELETE FROM login_attempts WHERE ip = ?');
    $stmt->bind_param('s', $ip);
    $stmt->execute();
    $stmt->close();
}

/**
 * Checks the credentials. Also upgrades any legacy plain-text password
 * to a proper hash the first time that admin logs in successfully.
 */
function verifyAdmin(mysqli $db, string $username, string $password): bool
{
    $stmt = $db->prepare('SELECT admin_pass FROM admin_user WHERE admin_name = ?');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->bind_result($stored);
    $found = $stmt->fetch();
    $stmt->close();

    if (!$found) {
        // Spend similar time whether or not the user exists.
        password_verify($password, password_hash('dummy-value', PASSWORD_DEFAULT));
        return false;
    }

    $isHash = !empty(password_get_info($stored)['algo']);
    $valid  = $isHash
        ? password_verify($password, $stored)
        : hash_equals($stored, $password);   // legacy plain-text row

    if ($valid && (!$isHash || password_needs_rehash($stored, PASSWORD_DEFAULT))) {
        $newHash = password_hash($password, PASSWORD_DEFAULT);
        $update  = $db->prepare('UPDATE admin_user SET admin_pass = ? WHERE admin_name = ?');
        $update->bind_param('ss', $newHash, $username);
        $update->execute();
        $update->close();
    }

    return $valid;
}

/* ---------- Handle the form ---------- */

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = is_string($_POST['admin_name'] ?? null) ? trim($_POST['admin_name']) : '';
    $password = is_string($_POST['admin_pass'] ?? null) ? $_POST['admin_pass'] : '';
    $token    = is_string($_POST['csrf_token'] ?? null) ? $_POST['csrf_token'] : '';
    $ip       = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        $error = 'Your session has expired. Please try again.';
    } elseif (isLockedOut($connTraining, $ip)) {
        $error = 'Too many failed attempts. Please try again in ' . LOCKOUT_MINUTES . ' minutes.';
    } elseif ($username !== '' && $password !== '' && verifyAdmin($connTraining, $username, $password)) {
        clearFailures($connTraining, $ip);
        session_regenerate_id(true);

        // A dedicated admin flag. Learner logins use different session keys,
        // so a learner can never pass the admin check.
        $_SESSION['is_admin']   = true;
        $_SESSION['admin_name'] = $username;

        header('Location: ' . LOGIN_SUCCESS_URL);
        exit;
    } else {
        recordFailure($connTraining, $ip);
        $error = 'Incorrect admin name or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Intermediate Licence Revision</title>
<link rel="stylesheet" href="../includes/admin.css">
</head>
<body>

<?php require __DIR__ . '/../includes/admin_header.php'; ?>
<main class="page">

<form class="panel" method="post" action="">
  <h1>Admin Login</h1>

  <?php if ($error !== ''): ?>
    <div class="error" role="alert"><?= h($error) ?></div>
  <?php endif; ?>

  <input type="hidden" name="csrf_token" value="<?= h($_SESSION['csrf_token']) ?>">

  <label for="admin_name">Admin name</label>
  <input type="text" id="admin_name" name="admin_name" maxlength="64"
         value="<?= h($_POST['admin_name'] ?? '') ?>"
         autocomplete="username" required autofocus>

  <label for="admin_pass">Admin password</label>
  <input type="password" id="admin_pass" name="admin_pass" maxlength="128"
         autocomplete="current-password" required>

  <div class="actions"><button type="submit" class="btn btn-primary">Log in</button></div>
</form>

</main>

<!-- Background slideshow: the image link is read by the script below -->
<div id="dwuserEasyBigBackground"
     style="position:fixed;left:0;right:0;top:0;bottom:0;z-index:-999;padding:0;margin:0;"
     data-ebbConfig="{opacity:0.7, delay:6000, loop:true, scaleMode:'fillArea', fadeInThreshold:3000, duration:1000, minScreenSize:481}"><a href="../Foundation/images/backdrops/02.jpg" rel="nofollow">&nbsp;</a></div>
<script src="../includes/easybigbackground.js"></script>

</body>
</html>
