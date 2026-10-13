<?php
declare(strict_types=1);

require_once '../includes/admin_auth.php';
require_once '../connections/connTraining.php';

const ADMIN_URL = 'options.php';   // main Admin page
const PER_PAGE  = 20;

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

/* Only a logged-in admin may view users. */
requireAdmin();

function h(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

$users      = [];
$total      = 0;
$page       = 1;
$totalPages = 1;
$error      = '';

try {
    mysqli_select_db($connTraining, $database_connTraining);

    $total      = (int) $connTraining->query('SELECT COUNT(*) FROM training_user')->fetch_row()[0];
    $totalPages = max(1, (int) ceil($total / PER_PAGE));

    $page = (int) filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1, 'default' => 1],
    ]);
    $page   = min($page, $totalPages);
    $limit  = PER_PAGE;
    $offset = ($page - 1) * PER_PAGE;

    $stmt = $connTraining->prepare(
        'SELECT email_address, first_name, last_name, last_accessed
         FROM training_user
         ORDER BY last_name, first_name
         LIMIT ? OFFSET ?'
    );
    $stmt->bind_param('ii', $limit, $offset);
    $stmt->execute();
    $stmt->bind_result($email, $first, $last, $accessed);
    while ($stmt->fetch()) {
        $users[] = [
            'email'    => $email,
            'first'    => $first,
            'last'     => $last,
            'accessed' => $accessed,
        ];
    }
    $stmt->close();
} catch (mysqli_sql_exception $e) {
    error_log('Show users failed: ' . $e->getMessage());
    $error = 'Sorry, the user list could not be loaded.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Show Users</title>
<link rel="stylesheet" href="../includes/admin.css">
</head>
<body>

<?php require __DIR__ . '/../includes/admin_header.php'; ?>
<main class="page">

<div class="panel panel--wide">
  <h1>Show Users</h1>

  <?php if ($error !== ''): ?>
    <div class="error" role="alert"><?= h($error) ?></div>
  <?php elseif ($total === 0): ?>
    <p class="count">No users are registered yet.</p>
  <?php else: ?>
    <p class="count"><?= $total ?> registered user<?= $total === 1 ? '' : 's' ?></p>

    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Email address</th>
            <th>First name</th>
            <th>Last name</th>
            <th>Last accessed</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($users as $user): ?>
            <tr>
              <td><?= h($user['email']) ?></td>
              <td><?= h($user['first']) ?></td>
              <td><?= h($user['last']) ?></td>
              <td><?= $user['accessed'] !== null ? h($user['accessed']) : 'Never' ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <?php if ($totalPages > 1): ?>
      <nav class="pager" aria-label="Pages">
        <?php if ($page > 1): ?>
          <a class="btn" href="?page=<?= $page - 1 ?>">Previous</a>
        <?php endif; ?>
        <span>Page <?= $page ?> of <?= $totalPages ?></span>
        <?php if ($page < $totalPages): ?>
          <a class="btn" href="?page=<?= $page + 1 ?>">Next</a>
        <?php endif; ?>
      </nav>
    <?php endif; ?>
  <?php endif; ?>

  <div class="actions">
    <a href="<?= h(ADMIN_URL) ?>" class="btn btn-primary">Done</a>
  </div>
</div>

</main>

<!-- Background slideshow: the image link is read by the script below -->
<div id="dwuserEasyBigBackground"
     style="position:fixed;left:0;right:0;top:0;bottom:0;z-index:-999;padding:0;margin:0;"
     data-ebbConfig="{opacity:0.7, delay:6000, loop:true, scaleMode:'fillArea', fadeInThreshold:3000, duration:1000, minScreenSize:481}"><a href="../Foundation/images/backdrops/02.jpg" rel="nofollow">&nbsp;</a></div>
<script src="../includes/easybigbackground.js"></script>

</body>
</html>
