<?php
declare(strict_types=1);

/**
 * Shared helpers for the learner-facing pages (Foundation now; Intermediate/Advanced later).
 * Save as includes/user_auth.php.
 */

// Report database errors as exceptions (they go to the server error log, not the browser).
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

const USER_LOGIN_URL = 'index.php';   // each learner area's own login page

function startAppSession(): void
{
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
}

if (!function_exists('h')) {
    function h(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

function postString(string $key): string
{
    return is_string($_POST[$key] ?? null) ? trim($_POST[$key]) : '';
}

function getInt(string $key, int $default, int $min, int $max): int
{
    $raw = is_string($_GET[$key] ?? null) ? $_GET[$key] : '';
    return (int) filter_var($raw, FILTER_VALIDATE_INT, [
        'options' => ['min_range' => $min, 'max_range' => $max, 'default' => $default],
    ]);
}

function csrfToken(): string
{
    startAppSession();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrfValid(string $token): bool
{
    startAppSession();
    return !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Logs a learner in if their email address is registered. Returns true on success.
 * Sets the same session keys the Intermediate/Advanced pages already use.
 */
function loginUser(mysqli $db, string $email): bool
{
    startAppSession();

    $stmt = $db->prepare('SELECT email_address FROM training_user WHERE email_address = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->bind_result($stored);
    $found = (bool) $stmt->fetch();
    $stmt->close();

    if (!$found) {
        return false;
    }

    session_regenerate_id(true);
    $_SESSION['MM_Username']      = $stored;
    $_SESSION['FT_Email_Address'] = $stored;
    $_SESSION['MM_UserGroup']     = '';
    return true;
}

/** Sends visitors who aren't logged in back to the login page. Returns the learner's email. */
function requireUser(): string
{
    startAppSession();

    $email = $_SESSION['MM_Username'] ?? '';
    if (!is_string($email) || $email === '') {
        header('Location: ' . USER_LOGIN_URL);
        exit;
    }

    header('Cache-Control: no-store');
    return $email;
}

function touchLastAccessed(mysqli $db, string $email): void
{
    $today = date('Y-m-d');
    $stmt  = $db->prepare('UPDATE training_user SET last_accessed = ? WHERE email_address = ?');
    $stmt->bind_param('ss', $today, $email);
    $stmt->execute();
    $stmt->close();
}
