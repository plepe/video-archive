<?php
$name = "Video Archive";
$id = "video-archive";
$depend = array(
  "modulekit-form",
  "modulekit-history",
  "modulekit-auth",
  "messages",
  "auth-pages",
  "PDOext",
); // use modulekit-form and all its requirements
$include = array(
  'php' => array(
    'src/php/Entity.php',
    'src/php/*.php' // automatically include all files in inc-directory
  ),
  'js' => array(
  ),
  'css' => array(
    'style.css' // include style.css
  )
);
