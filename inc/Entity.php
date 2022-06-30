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

    $this->isLoaded = false;
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
    $default_overrides = [];
    $userId = $auth->current_user()->id();

    $res = $db->query('select * from entity_access where id=' . $db->quote($this->id) . ' and ' . $db->quoteIdent("access_{$type}") . '=true');
    while ($elem = $res->fetch()) {
      if ($auth->access($elem['user'])) {
        return true;
      }
    }

    $res = $db->query('select * from entity_access where id=' . $db->quote($this->id) . ' and ' . $db->quoteIdent("access_{$type}") . '=false');
    while ($elem = $res->fetch()) {
      // Explicit deny for this user
      if ($elem['user'] === $userId) {
        return false;
      }

      // Might be denied
      if ($auth->access($elem['user'])) {
        $default_overrides[] = $elem['user'];
      }
    }

    global $default_access;
    foreach ($default_access as $user => $rights) {
      if (in_array($type, $rights) && $auth->access($user) && !in_array($user, $default_overrides)) {
        return true;
      }
    }

    return false;
  }

  static function list ($options = []) {
    global $db;

    $qry = $db->query('select * from entity');
    while ($elem = $qry->fetch()) {
      switch ($elem['type']) {
        case 'video':
          $entity = new Video($elem['id'], $elem);
          break;
      }

      if ($entity->access('list')) {
        $entity->load();
        yield $entity;
      }
    }
  }

  static function get ($id, $options = []) {
    global $db;

    $qry = $db->query('select * from entity where id=' . $db->quote($id));
    $res = $qry->fetchAll();

    if (sizeof($res)) {
      $elem = $res[0];

      switch ($elem['type']) {
        case 'video':
          $entity = new Video($elem['id'], $elem);
          break;
        default:
          throw new Exception('Invalid entity type');
      }

      $entity->load();

      return $entity;
    }

    return null;
  }
}
