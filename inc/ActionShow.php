<?php
class ActionShow {
  function __construct ($id) {
    $this->id = $id;
  }

  function show ($options = []) {
    $result = "";

    $video = Video::get($this->id);

    if (!$video->access('view')) {
      return 'Access denied';
    }

    $result .= $video->showFull($options);

    $result .= "<div class='menu actions'><ul>";
    $result .= "<li class='edit'><a href=\"?id={$this->id}&amp;action=edit\">edit</a></li>";
    $result .= "</ul></div>";

    return $result;
  }
}
