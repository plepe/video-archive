<?php
class Share extends Entity {
  public static $dbFields = ['reference'];
  public static $dbTable = 'share';

  function formEdit () {
    return [
      'reference' => [
        'name' => 'Reference',
        'type' => 'text',
      ],
    ];
  }

  function _load () {
    global $db;

    $qry = $db->query('select * from share where id=' . $db->quote($this->id));
    $res = $qry->fetchAll();
    $this->data = array_merge($this->data, $res[0]);
  }

  function showTeaser ($options = []) {
    return '';
  }

  function showFull ($options = []) {
    $entity = Entity::get($this->data['reference']);

    return $entity->showFull($options);
  }
}
