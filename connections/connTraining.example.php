<?php
/**
 * Database connection TEMPLATE for the CDARS training site.
 *
 * This file is deliberately named ".example" so unzipping the pack can never overwrite
 * your real connection file. To use it on a new server, copy it to
 * connections/connTraining.php and fill in the four details below.
 */
$hostname_connTraining = 'localhost';
$database_connTraining = 'your_database_name';
$username_connTraining = 'your_database_user';
$password_connTraining = 'your_database_password';

// Report connection problems as exceptions, so the details go to the server error log
// instead of the visitor's screen.
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $connTraining = mysqli_connect(
        $hostname_connTraining,
        $username_connTraining,
        $password_connTraining,
        $database_connTraining
    );
} catch (mysqli_sql_exception $e) {
    error_log('Database connection failed: ' . $e->getMessage());
    http_response_code(503);
    exit('Sorry, the training site is temporarily unavailable. Please try again later.');
}
