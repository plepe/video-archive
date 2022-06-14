<?php
$name = "Video Archive";
$id = "video-archive";
$depend = array(
  "modulekit-form",
  "PDOext",
); // use modulekit-form and all its requirements
$include = array(
  'php' => array(
    'inc/*.php' // automatically include all files in inc-directory
  ),
  'js' => array(
    'inc/*.js' // automatically include all files in inc-directory
  ),
  'css' => array(
    'style.css' // include style.css
  )
);
