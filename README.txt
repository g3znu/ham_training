CDARS training site - file guide
================================

Unzip this over your training folder (the one that holds index.php). Folder names:

  index.php            top-level page (Exam Revision Questions)
  Foundation/          Foundation pages, plus images/ (backdrop, question figures, slide images)
                       and slides/ (the tutorial slide pages)
  Intermediate/        Intermediate pages, plus images/ and slides/ (same layout)
  Advanced/            Full Level page (the folder keeps its old name so existing links still work)
  Admin/               admin area (log in with admin_login.php)
  includes/            shared code, stylesheet, slideshow script and banner

FOUR THINGS THIS ZIP DOES NOT CONTAIN - they must already be on the server:

  connections/connTraining.php      the database connection (left out because it holds the password;
                                      a password-free template, connTraining.example.php, is in this zip
                                      and can never overwrite your real file)
  includes/CDARS_logo.jpg           the club logo - copy it here from where it is now
  Styles/                           the slide viewer's stylesheets:
                                      Styles/dmxHTML5Slideshow.css
                                      Styles/dmxHTML5Slideshow/dark_matter/dark_matter.css
  ScriptLibrary/                    the slide viewer's scripts:
                                      jquery-latest.pack.js, jquery.easing.1.3.js, dmxHTML5Slideshow.js

  (The slide pages in Foundation/slides and Intermediate/slides load the Styles and ScriptLibrary
  files, so keep both folders where they are.)

DATABASE TABLES USED

  admin_user, training_user, training_test, training_tracker,
  training_foundation, training_intermediate, login_attempts

QUESTION FIGURES

  The question pages show pictures named in each question's "image" column, from
  Foundation/images and Intermediate/images (for example "01 Cell" is shown as 01 Cell.jpg).
  The pages always add ".jpg", so a figure saved as .jpeg will not appear.

OLD FILES TO REMOVE FROM THE LIVE SITE (no longer used, and some run without any login)

  includes/real_string.php           (nothing in this pack uses it; keep it only if other pages on
                                      your site do)
  Foundation/check_answers2.php, Intermediate/check_answers2.php
  add_q_to_database.php, add_sections_to_training.php, fill_training_tests.php,
  random_questions.php, random_questions_html.php, print_questions.php   (any folder)
  Intermediate/Intermediate.txt      (the question bank with answers marked - downloadable by anyone)
  slides/_notes/dwsync.xml

IMAGES REMOVED FROM THIS PACK BECAUSE NO PAGE USES THEM

  Foundation/images/backdrops/   all except 02.jpg (the one background the pages use)
  Intermediate/images/backdrops/ the whole folder (identical copies of the Foundation ones)
  Foundation/images/14 Yagi.jpeg
  Foundation/images/06 Resistor.jpg, head.jpg, head2.jpg   (no question uses them)
  Intermediate/images/CARS/05/05-16, 07/07-16, 07/07-17, 09/09-13, 09/09-14,
                           15/15-09, 15/15-10, 16/16-09  (.jpg)
