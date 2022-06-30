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

    $result .= $this->entity->showFull($options);

    $result .= "<div class='menu actions'><ul>";
    $result .= "<li class='edit'><a href=\"?id={$this->id}&amp;action=edit\">edit</a></li>";
    $result .= "</ul></div>";

    return $result;
  }
}
