<?php
declare(strict_types=1);

require_once '../includes/admin_auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$token = is_string($_POST['csrf_token'] ?? null) ? $_POST['csrf_token'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && !empty($_SESSION['csrf_token'])
    && hash_equals($_SESSION['csrf_token'], $token)) {

    $_SESSION = [];

    $p = session_get_cookie_params();
    setcookie(session_name(), '', [
        'expires'  => time() - 3600,
        'path'     => $p['path'],
        'domain'   => $p['domain'],
        'secure'   => $p['secure'],
        'httponly' => $p['httponly'],
        'samesite' => $p['samesite'] ?: 'Lax',
    ]);

    session_destroy();
    header('Location: ' . ADMIN_LOGIN_URL);
    exit;
}

// Not a valid logout request: go back to the admin page.
header('Location: options.php');
exit;
