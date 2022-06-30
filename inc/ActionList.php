<?php
class ActionList {
  function access () {
    return default_access('list');
  }

  function show ($options = []) {
    $result = "";

    foreach (Entity::list() as $entity) {
      $result .= $entity->showTeaser($options);
    }

    return $result;
  }
}
