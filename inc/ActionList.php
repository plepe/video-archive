<?php
class ActionList {
  function access () {
    return default_access('list');
  }

  function show ($options = []) {
    $result = "";

    foreach (Video::list() as $video) {
      $result .= $video->showTeaser($options);
    }

    return $result;
  }
}
