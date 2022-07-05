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

    if (!$this->entity->data['ready']) {
      messages_add('Video has not been fully processed yet', MSG_NOTICE);
    }

    return $this->entity->showFull($options);
  }

  function menu () {
    $result = [];

    if ($this->entity->access('update')) {
      $result[] = [
        'url' => "?id={$this->id}&action=edit",
        'text' => 'edit',
      ];
    }

    return $result;
  }
}
