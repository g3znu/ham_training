<?php
declare(strict_types=1);

require_once '../includes/user_auth.php';

requireUser();

$brandRoot     = '../';
$headerSection = 'Foundation training';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Foundation Licence Questions</title>
<link rel="stylesheet" href="../includes/admin.css">
</head>
<body>

<?php require __DIR__ . '/../includes/admin_header.php'; ?>
<main class="page">

  <div class="panel panel--slim">
    <h1>Foundation Licence Questions</h1>

    <p>Please note that these documents will help with the answers:</p>
    <ul class="link-list">
      <li>
        <strong>Amateur Radio Licence, Guidance for Licensees</strong><br>
        <a href="https://www.ofcom.org.uk/__data/assets/pdf_file/0026/82637/amateur_radio_licence_guidance_for_licensees.pdf" rel="noopener">Download from Ofcom</a>
      </li>
      <li>
        <strong>Section 2, Terms, Conditions and Limitations</strong><br>
        <a href="https://www.ofcom.org.uk/__data/assets/pdf_file/0027/62991/amateur-terms.pdf" rel="noopener">Download from Ofcom</a>
      </li>
      <li>
        <strong>Reference Data for use in the Foundation Level Examination (EX307)</strong><br>
        <a href="http://rsgb.org/main/files/2015/09/EX307-Foundation-Reference-Booklet.pdf" rel="noopener">Download from the RSGB</a>
      </li>
      <li><strong>Foundation Licence Manual</strong></li>
    </ul>

    <div class="actions">
      <a class="btn btn-primary" href="main.php">Proceed</a>
    </div>

    <p class="fine">My thanks to hamtests.co.uk for the use of their question bank.</p>
  </div>

</main>

<?php require __DIR__ . '/../includes/site_background.php'; ?>

</body>
</html>
