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

    if (array_key_exists('delete', $_REQUEST)) {
      if (!$this->entity->access('delete')) {
        reload(url([ 'id' => $this->entity->id ]));
        messages_add("Access denied", MSG_ERROR);
        return '';
      }

      if (array_key_exists('delete-verify', $_REQUEST)) {
        $this->entity->remove();
        reload(url(['action' => 'list']));
        messages_add("Deleted.", MSG_NOTICE);
        return '';
      }
      else if (array_key_exists('delete-rebut', $_REQUEST)) {
        reload(url([ 'id' => $this->entity->id ]));
        messages_add("Not deleted.", MSG_NOTICE);
        return "";
      }
      else {
        $text  = '<form enctype="multipart/form-data" method="post">';
        $text .= 'Really delete?<br>';
        $text .= '<input type="hidden" name="delete" value="delete"/>';
        $text .= '<input type="submit" name="delete-verify" value="Yes"/>';
        $text .= '<input type="submit" name="delete-rebut" value="No"/>';
        $text .= '</form>';
        return $text;
      }
    }

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
    $text .= '<input type="submit" value="Save"/>';
    if ($this->entity->access('delete')) {
      $text .= '<input type="submit" name="delete" value="Delete"/>';
    }
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
