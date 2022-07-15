<?php
class ActionNewShare {
  function access () {
    return default_access('create');
  }

  function show () {
    global $data_dir;
    $entity = new Share();

    $form = new form('data', $entity->formEdit());

    if (array_key_exists('reference', $_REQUEST)) {
      $data['reference'] = $_REQUEST['reference'];
    }

    $form->set_data($data);

    if ($form->is_complete()) {
      $data = $form->save_data();

      $changeset = new Changeset('new share');
      $changeset->open();
      $result = $entity->save($data, $changeset);
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
