<?php
class ActionList {
  function show ($options = []) {
    $result = "";

    foreach (Video::list() as $video) {
      $result .= $video->showTeaser($options);
    }

    return $result;
  }
}
