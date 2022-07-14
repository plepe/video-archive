<?php
class ActionEdit {
  function __construct ($id) {
    $this->id = $id;
    $this->entity = Entity::get($this->id);
  }

  function access () {
    return $this->entity->access('update');
  }

  function show () {
    global $data_dir;

    $form = new form('data', $this->entity->formEdit());

    $data = $this->entity->data;
    $data['file'] = [
      'name' => 'video.mp4',
      'type' => 'video/mp4',
      'size' => $data['filesize'],
    ];

    if ($form->is_empty()) {
      $form->set_data($data);
    }

    if ($form->is_complete()) {
      mkdir("{$data_dir}/{$this->entity->id}");
      $data = $form->save_data();

      $changeset = new Changeset('edit video');

      $changeset->open();
      $result = $this->entity->save($data, $changeset);
      $changeset->commit();

      if ($result) {
        reload(url([ 'id' => $this->entity->id ]));
        messages_add("Saved.", MSG_NOTICE);
        return "";
      }
      else {
        messages_add("An error occured.", MSG_ERROR);
        return "";
      }
    }

    $text  = '<form enctype="multipart/form-data" method="post">';
    $text .= $form->show();
    $text .= '<input type="submit" value="Submit"/>';
    $text .= '</form>';

    return $text;
  }

  function menu () {
    $result = [];

    if ($this->entity->access('view')) {
      $result[] = [
        'url' => [ 'id' => $this->id, 'action' => 'show' ],
        'text' => 'view',
      ];
    }

    return $result;
  }
}
