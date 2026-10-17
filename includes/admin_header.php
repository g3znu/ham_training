<?php
/**
 * Shared branded header banner. Save as includes/admin_header.php.
 *
 * Set these before including it if the page isn't in a first-level folder:
 *   $brandRoot     path from the page to the app root ('' in the root, '../' one folder down)
 *   $headerSection text shown at the right of the banner
 *
 * The logo file is includes/CDARS_logo.jpg.
 */
$brandRoot     = $brandRoot ?? '../';
$headerSection = $headerSection ?? 'Training admin';
?>
<header class="site-header">
  <?php /* Opens in a new tab so a learner's current set of questions isn't lost. */ ?>
  <a href="https://www.g3mdg.org.uk/" target="_blank" rel="noopener"
     title="Chesham &amp; District Amateur Radio Society website">
    <img src="<?= htmlspecialchars($brandRoot, ENT_QUOTES, 'UTF-8') ?>includes/CDARS_logo.jpg"
         alt="Chesham &amp; District Amateur Radio Society logo">
  </a>
  <div>
    <div class="name">Chesham &amp; District Amateur Radio Society</div>
    <div class="callsigns">G3CHR - G3MDG - G1MDG</div>
  </div>
  <div class="section"><?= htmlspecialchars($headerSection, ENT_QUOTES, 'UTF-8') ?></div>
</header>
