<?php
class ActionNewCollection {
  function access () {
    return default_access('create');
  }

  function show () {
    global $data_dir;
    $collection = new Collection();

    $form = new form('data', $collection->formEdit());

    if ($form->is_complete()) {
      $data = $form->save_data();

      $changeset = new Changeset('new collection');
      $changeset->open();
      $result = $collection->save($data, $changeset);
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

      return "Saved";
    }

    $text  = '<form enctype="multipart/form-data" method="post">';
    $text .= $form->show();
    $text .= '<input type="submit" value="Submit"/>';
    $text .= '</form>';

    return $text;
  }
}
