<?php
declare(strict_types=1);
 
// This page sits in the app root, so the shared header needs these two settings.
$brandRoot     = '';
$headerSection = 'Licence training';
 
// folder/page => [button label, short description]
$courses = [
    'Foundation/index.php'   => ['Foundation Training',   'Tutorial slides and exam questions.'],
    'Intermediate/index.php' => ['Intermediate Training', 'Tutorial slides and exam questions.'],
    'Advanced/index.php'     => ['Full Level Training',   'Links to training material and the RSGB question bank.'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Licence Training – Chesham &amp; District Amateur Radio Society</title>
<link rel="stylesheet" href="includes/admin.css?v=<?= (int) @filemtime(__DIR__ . '/includes/admin.css') ?>">
</head>
<body>
 
<?php require __DIR__ . '/includes/admin_header.php'; ?>
<main class="page">
 
  <div class="panel panel--slim">
    <h1>Exam Revision Questions</h1>
 
    <p>
      The UK Amateur Radio Licence has three levels, Foundation, Intermediate and Full Level.
      It is a progressive licence with increasing access to the spectrum and power levels as you
      progress through the licence classes.
    </p>
    <p>
      In order to obtain an Amateur Radio licence you need to demonstrate a level of technical
      knowledge and expertise relevant to the licence class.
    </p>
    <p>
      <strong>Foundation</strong> &ndash; this is the entry level for those starting in the hobby.
      The Foundation level exam consists of 26 questions with a pass mark of 19. The exam questions
      are written to test basic understanding of radio technology, propagation and safety.
    </p>
    <p>
      <strong>Intermediate</strong> &ndash; permits licensees to use high power levels, so you need
      to demonstrate greater technical competence. The exam covers more detailed technical aspects
      of receiver and transmitter design, antennas and matching. The exam has 46 questions and the
      pass mark is 28.
    </p>
    <p>
      <strong>Full Level</strong> &ndash; the full level licence permits the use of high transmitter
      power levels and access to all the amateur frequency bands. It also allows licensees to use
      their equipment in some other countries, although some countries may require you to apply for
      a specific &ldquo;reciprocal licence&rdquo;. The exam has a very broad syllabus and consists of
      58 questions with a pass mark of 35.
    </p>
    <p>
      The CDARS revision questions cover the Foundation and Intermediate exams. Some information and
      pointers towards other resources for the Full Level exam are also covered.
    </p>
 
    <p class="intro"><strong>Please select the required licence training.</strong></p>
 
    <?php foreach ($courses as $url => [$label, $description]): ?>
      <a href="<?= htmlspecialchars($url, ENT_QUOTES, 'UTF-8') ?>" class="btn btn-block btn-primary"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></a>
      <p class="fine"><?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endforeach; ?>
  </div>
 
</main>
 
<?php require __DIR__ . '/includes/site_background.php'; ?>
 
</body>
</html>