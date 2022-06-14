<?php
class Video {
  function __construct ($id = null) {
    if (!$id) {
      $this->id = generateId();
    } else {
      $this->id = $id;
    }
  }

  function save ($data, $changeset) {
    global $db;
  }
}
