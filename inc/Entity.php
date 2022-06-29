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

  // type: view, list, update, delete
  function access ($type) {
    global $auth;
    global $db;

    $res = $db->query('select * from entity_access where id=' . $db->quote($this->id) . ' and ' . $db->quoteIdent("access_{$type}") . '=true');
    while ($elem = $res->fetch()) {
      if ($auth->access($elem['user'])) {
        return true;
      }
    }

    global $default_access;
    foreach ($default_access as $user => $rights) {
      if (in_array($type, $rights) && $auth->access($user)) {
        return true;
      }
    }

    return false;
  }
}
