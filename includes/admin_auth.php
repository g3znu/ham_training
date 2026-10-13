<?php
declare(strict_types=1);

/**
 * Shared access check for every admin page.
 * Save as ../includes/admin_auth.php and call requireAdmin() at the top of each admin page.
 */

const ADMIN_LOGIN_URL = 'admin_login.php';   // <-- change to the filename of your admin login page

function requireAdmin(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (empty($_SESSION['is_admin'])) {
        header('Location: ' . ADMIN_LOGIN_URL);
        exit;
    }

    // Stops the back button showing admin pages after logging out.
    header('Cache-Control: no-store');
}
