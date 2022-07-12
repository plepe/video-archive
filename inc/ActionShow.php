<?php
class ActionShow {
  function __construct ($id) {
    $this->id = $id;
    $this->entity = Entity::get($this->id);
  }

  function access () {
    return $this->entity->access('view');
  }

  function show ($options = []) {
    $result = "";

    return $this->entity->showFull($options);
  }

  function menu () {
    $result = [];

    if ($this->entity->access('update')) {
      $result[] = [
        'url' => [ 'id' => $this->id, 'action' => 'edit' ],
        'text' => 'edit',
      ];
    }

    return $result;
  }
}
