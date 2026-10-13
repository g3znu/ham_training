<?php
declare(strict_types=1);

require_once '../includes/user_auth.php';

requireUser();

$brandRoot     = '../';
$headerSection = 'Foundation training';

$slides = [
    'slides/slide-01.php' => '1. Welcome to Amateur Radio',
    'slides/slide-02.php' => '2. Technical Basics',
    'slides/slide-03.php' => '3. Transmitters & Receivers',
    'slides/slide-04.php' => '4. Feeders & Antennas',
    'slides/slide-05.php' => '5. Propagation',
    'slides/slide-06.php' => '6. Licence Conditions',
    'slides/slide-07.php' => '7. EMC',
    'slides/slide-08.php' => '8. Safety',
    'slides/slide-09.php' => '9. Operating Procedures',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Foundation Licence Slides</title>
<link rel="stylesheet" href="../includes/admin.css">
</head>
<body>

<?php require __DIR__ . '/../includes/admin_header.php'; ?>
<main class="page">

  <div class="panel panel--menu">
    <h1>Foundation Licence Slides</h1>

    <?php foreach ($slides as $url => $label): ?>
      <a class="btn btn-block" href="<?= h($url) ?>" onclick="return openSlides(this.href)"><?= h($label) ?></a>
    <?php endforeach; ?>

    <a class="btn btn-block btn-primary" href="slides_or_exam.php">Back to Foundation main menu</a>
  </div>

</main>

<script>
// Slides open in their own window, centred on the screen.
function openSlides(url) {
  var w = 820, h = 620;
  var left = Math.round((screen.availWidth - w) / 2);
  var top  = Math.round((screen.availHeight - h) / 2);
  window.open(url, 'slides', 'width=' + w + ',height=' + h + ',status,resizable,left=' + left + ',top=' + top);
  return false;
}
</script>

<?php require __DIR__ . '/../includes/site_background.php'; ?>

</body>
</html>
