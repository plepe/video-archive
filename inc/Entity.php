<?php
class Entity {
  public static $dbEntityFields = ['ready'];
  static $cache = [];

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

    $entityFields = [];
    foreach ($this::$dbEntityFields as $field) {
      if (array_key_exists($field, $data)) {
        $entityFields[$field] = $data[$field];
      }
    }

    $fields = [];
    foreach ($this::$dbFields as $field) {
      if (array_key_exists($field, $data)) {
        $fields[$field] = $data[$field];
      }
    }

    if ($this->isNew) {
      $entityFields['id'] = $this->id;
      $entityFields['type'] = get_class($this);
      $entityFields['author'] = 'test';

      $db->query(dbCompileInsert('entity', $entityFields));

      $fields['id'] = $this->id;
      $db->query(dbCompileInsert($this::$dbTable, $fields));

      $this->isNew = false;
    }
    else {
      $entityFields['tsUpdate'] = (new DateTime())->format('Y-m-d G:i:s');

      $db->query(dbCompileUpdate('entity', $entityFields, ['id' => $this->id]));

      if (sizeof($fields)) {
        $db->query(dbCompileUpdate('video', $fields, ['id' => $this->id]));
      }
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

  function queue ($func, $options=null) {
    global $db;

    $res = $db->query('insert into process_queue (id, func, options) values (' . $db->quote($this->id) . ', ' . $db->quote($func) . ', ' . $db->quote(json_encode($options)) . ')');
  }

  static function list ($options = []) {
    global $db;

    $qry = $db->query('select * from entity');
    while ($elem = $qry->fetch()) {
      if (array_key_exists($elem['id'], Entity::$cache)) {
        $entity = Entity::$cache[$elem['id']];
      }
      else {
        switch ($elem['type']) {
          case 'Video':
            $entity = new Video($elem['id'], $elem);
            break;
        }
      }

      Entity::$cache[$elem['id']] = $entity;

      if ($entity->access('list')) {
        $entity->load();
        yield $entity;
      }
    }
  }

  static function get ($id, $options = []) {
    global $db;

    if (array_key_exists($id, Entity::$cache)) {
      $entity = Entity::$cache[$id];
      $entity->load();
      return $entity;
    }

    $qry = $db->query('select * from entity where id=' . $db->quote($id));
    $res = $qry->fetchAll();

    if (sizeof($res)) {
      $elem = $res[0];

      switch ($elem['type']) {
        case 'Video':
          $entity = new Video($elem['id'], $elem);
          break;
        default:
          throw new Exception('Invalid entity type');
      }

      Entity::$cache[$elem['id']] = $entity;
      $entity->load();

      return $entity;
    }

    return null;
  }
}
