<?php
class ActionList {
  function show ($options = []) {
    $result = "";

    foreach (Video::get() as $video) {
      $result .= $video->show($options);
    }

    return $result;
  }
}
