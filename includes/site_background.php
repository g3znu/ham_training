<?php
/**
 * The club slideshow background. Save as includes/site_background.php.
 * Set $brandRoot (path from the page to the app root) before including.
 */
$brandRoot = $brandRoot ?? '../';
$bgImage   = htmlspecialchars($brandRoot . 'Foundation/images/backdrops/02.jpg', ENT_QUOTES, 'UTF-8');
$bgScript  = htmlspecialchars($brandRoot . 'includes/easybigbackground.js', ENT_QUOTES, 'UTF-8');
?>
<!-- Background slideshow: the image link is read by the script below -->
<div id="dwuserEasyBigBackground"
     style="position:fixed;left:0;right:0;top:0;bottom:0;z-index:-999;padding:0;margin:0;"
     data-ebbConfig="{opacity:0.7, delay:6000, loop:true, scaleMode:'fillArea', fadeInThreshold:3000, duration:1000, minScreenSize:481}"><a href="<?= $bgImage ?>" rel="nofollow">&nbsp;</a></div>
<script src="<?= $bgScript ?>"></script>
