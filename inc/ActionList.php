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

  function menu () {
    $result = [];

    if (default_access('create')) {
      $result[] = [
        'url' => [ 'action' => 'newVideo' ],
        'text' => 'Upload Video',
      ];
      $result[] = [
        'url' => "?action=newPlaylist",
        'text' => 'Create Playlist',
      ];
    }

    return $result;
  }
}
