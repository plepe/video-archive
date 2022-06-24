<?php
class ActionShow {
  function __construct ($id) {
    $this->id = $id;
  }

  function show ($options = []) {
    $result = "";

    $video = Video::get($this->id);
    $result .= $video->showFull($options);

    return $result;
  }
}
