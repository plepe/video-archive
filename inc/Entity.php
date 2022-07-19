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

  function formEdit () {
    return [
      'title'   => [
        'type'    => 'text',
        'name'    => 'Title',
      ],
      'access_anonymous' => [
        'type' => 'select',
        'name' => 'Anonymous Access',
        'values' => [
          'default' => 'default',
          'private' => 'private',
          'public' => 'public',
        ],
      ],
    ];
  }

  function load () {
    global $db;

    if ($this->isLoaded) {
      return;
    }

    $qry = $db->query(dbCompileSelect('entity', [ 'id' => $this->id ]));
    $res = $qry->fetchAll();
    $this->data = array_merge($this->data, $res[0]);

    $qry = $db->query(dbCompileSelect('entity_access', [ 'id' => $this->id ]));
    $this->data['access'] = [];
    while ($elem = $qry->fetch()) {
      $this->data['access'][$elem['user']] = [
        'view' => to_bool($elem['access_view']),
        'list' => to_bool($elem['access_list']),
        'update' => to_bool($elem['access_update']),
        'delete' => to_bool($elem['access_delete']),
      ];
    }

    if (method_exists($this, '_load')) {
      $this->_load();
    }

    $this->isLoaded = true;
  }

  function dataPreEdit () {
    $result = $this->data;

    $result['access_anonymous'] = 'default';
    if (array_key_exists('', $this->data['access'])) {
      $access = $this->data['access'][''];
      if ($access['view'] === false || $access['list'] === false) {
        $result['access_anonymous'] = 'private';
      }
      else if ($access['view'] === true || $access['list'] === true) {
        $result['access_anonymous'] = 'public';
      }
      else {
        $result['access_anonymous'] = null;
      }
    }

    return $result;
  }

  function dataPostEdit ($data) {
    $result = $data;

    if (array_key_exists('access_anonymous', $data)) {
      switch ($data['access_anonymous']) {
        case 'private':
          $result['access'][''] = [ 'view' => false, 'list' => false, 'update' => false, 'delete' => false ];
          break;
        case 'public':
          $result['access'][''] = [ 'view' => true, 'list' => true, 'update' => null, 'delete' => null ];
          break;
        case 'default':
          unset($result['access']['']);
          break;
        default:
      }

      unset($result['access_anonymous']);
    }

    return $result;
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
      $entityFields['class'] = get_class($this);
      $entityFields['author'] = 'test';

      $db->query(dbCompileInsert('entity', $entityFields));

      $fields['id'] = $this->id;
      print dbCompileInsert($this::$dbTable, $fields);
      $db->query(dbCompileInsert($this::$dbTable, $fields));

      $this->isNew = false;
    }
    else {
      $entityFields['tsUpdate'] = (new DateTime())->format('Y-m-d G:i:s');

      $db->query(dbCompileUpdate('entity', $entityFields, ['id' => $this->id]));

      if (sizeof($fields)) {
        $db->query(dbCompileUpdate($this::$dbTable, $fields, ['id' => $this->id]));
      }
    }

    if (array_key_exists('access', $data)) {
      $db->query(dbCompileRemove('entity_access', ['id' => $this->id]));
      foreach ($data['access'] as $user => $access) {
        $db->query(dbCompileInsert('entity_access', [
          'user' => $user,
          'id' => $this->id,
          'access_view' => $access['view'],
          'access_list' => $access['list'],
          'access_update' => $access['update'],
          'access_delete' => $access['delete'],
        ]));
      }
    }

    return true;
  }

  function remove () {
    global $db;

    if (isset($this::$dbTable)) {
      $db->query(dbCompileRemove($this::$dbTable, ['id' => $this->id]));
    }

    $db->query(dbCompileRemove('entity', ['id' => $this->id]));
  }

  // type: view, list, update, delete
  function access ($type) {
    global $auth;
    global $db;
    $default_overrides = [];
    $userId = $auth->current_user()->id();

    if (get_class($this) !== 'Share' && array_key_exists('share', $_REQUEST)) {
      $share = Share::get($_REQUEST['share']);
      if (!$this->isPartOf($share)) {
        return false;
      }

      return $share->access($type);
    }

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

  function references () {
    global $db;

    $qry = $db->query(dbCompileSelect('playlist_video', [ 'video_id' => $this->id ]));
    $result = [];
    while ($elem = $qry->fetch()) {
      $result[] = $elem['playlist_id'];
    }

    $qry = $db->query(dbCompileSelect('share', [ 'reference' => $this->id ]));
    while ($elem = $qry->fetch()) {
      $result[] = $elem['id'];
    }

    return $result;
  }

  function isPartOf ($entity) {
    $references = $this->references();
    if (in_array($entity->id, $references)) {
      return true;
    }

    foreach ($references as $ref) {
      if (Entity::get($ref)->isPartOf($entity)) {
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
      elseif (!is_subclass_of($elem['class'], 'Entity')) {
        throw new Exception("{$elem['class']} is not a derivative of Entity");
      }
      else {
        $class = $elem['class'];
        $entity = new $class($elem['id'], $elem);
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

      if (!is_subclass_of($elem['class'], 'Entity')) {
        throw new Exception("{$elem['class']} is not a derivative of Entity");
      }
      else {
        $class = $elem['class'];
        $entity = new $class($elem['id'], $elem);
      }

      Entity::$cache[$elem['id']] = $entity;
      $entity->load();

      return $entity;
    }

    return null;
  }
}
