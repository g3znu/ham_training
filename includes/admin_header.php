<?php
/**
 * Shared branded header banner. Save as includes/admin_header.php.
 *
 * Set these before including it if the page isn't in the Admin2 folder:
 *   $brandRoot     path from the page to the app root ('' in the root, '../' one folder down)
 *   $headerSection text shown at the right of the banner
 */
$brandRoot     = $brandRoot ?? '../';
$headerSection = $headerSection ?? 'Training admin';
?>
<header class="site-header">
  <a href="<?= htmlspecialchars($brandRoot, ENT_QUOTES, 'UTF-8') ?>index.php">
    <img src="<?= htmlspecialchars($brandRoot, ENT_QUOTES, 'UTF-8') ?>Admin2/CDARS_logo.jpg"
         alt="Chesham &amp; District Amateur Radio Society logo">
  </a>
  <div>
    <div class="name">Chesham &amp; District Amateur Radio Society</div>
    <div class="callsigns">G1MDG – G3MDG</div>
  </div>
  <div class="section"><?= htmlspecialchars($headerSection, ENT_QUOTES, 'UTF-8') ?></div>
</header>
