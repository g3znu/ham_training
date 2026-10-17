<?php
declare(strict_types=1);

// A simple page of links: no login or database needed.
$brandRoot     = '../';
$headerSection = 'Full Level training';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Welcome to the Full Level Self-Training</title>
<link rel="stylesheet" href="../includes/admin.css?v=<?= (int) @filemtime(__DIR__ . '/../includes/admin.css') ?>">
</head>
<body>

<?php require __DIR__ . '/../includes/admin_header.php'; ?>
<main class="page">

  <div class="panel panel--slim">
    <h1>Full Level Licence Learning &amp; Questions</h1>

    <p>
      The Full Level exam is broad in scope and very technical in nature. CDARS does not publish
      its own revision questions for this exam level, but the RSGB has published the complete
      question bank, and this is linked below.
    </p>
    <p>
      Our thanks also to <strong>Bredhurst Receiving and Transmitting Society (B.R.A.T.S.)</strong>
      for allowing us to link to their Full Level training pages.
    </p>

    <a class="btn btn-block btn-primary" href="https://brats-qth.org/training/adv/index.htm" rel="noopener" target="_blank" >Training</a>
    <a class="btn btn-block btn-primary" href="https://rsgb.services/public/exams/eqdb/?exam=2025+Full+V1.6b&amp;images=true&amp;randomise=true" rel="noopener" target="_blank" >RSGB Full Level questions</a>
    <a class="btn btn-block" href="../index.php">Return to Training main menu</a>
  </div>

</main>

<?php require __DIR__ . '/../includes/site_background.php'; ?>

</body>
</html>
