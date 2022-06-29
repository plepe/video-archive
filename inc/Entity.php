<?php
class Entity {
  function __construct ($id = null, $data = null) {
    if (!$id) {
      $this->id = generateId();
      $this->isNew = true;
    } else {
      $this->id = $id;
      $this->data = $data;
      $this->isNew = false;
    }
  }

  function save ($data, $changeset) {
    global $db;

    if ($this->isNew) {
      $db->query('insert into entity (' . $db->quoteIdent('id') . ', `author`) values (' . $db->quote($this->id) . ', \'test\')');
    }
  }
}
