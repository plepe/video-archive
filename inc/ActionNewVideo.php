<?php
class ActionNewVideo {
  function access () {
    return default_access('create');
  }

  function show () {
    global $data_dir;
    $video = new Video();

    $form = new form('data', [
      'title'   => [
        'type'    => 'text',
        'name'    => 'Title',
      ],
      'file'    => [
        'type'    => 'file',
        'name'    => 'Video file',
        'path'    => "{$data_dir}/{$video->id}",
        'template' => 'original.[ext]',
        'req'     => true,
      ],
      'date'    => [
        'type'    => 'datetime',
        'name'    => 'Date',
      ],
    ]);

    if ($form->is_complete()) {
      mkdir("{$data_dir}/{$video->id}");
      $data = $form->save_data();
      $data['ready'] = false;
      $data['filesize'] = $data['file']['size'];
      $data['originalFile'] = $data['file']['name'];

      $changeset = new Changeset('new video');
      $changeset->open();
      $result = $video->save($data, $changeset);
      $changeset->commit();

      if ($result) {
        $video->queue('Create', []);
        reload(url([ 'id' => $this->entity->id ]));
        messages_add("Saved.", MSG_NOTICE);
        return "";
      }
      else {
        messages_add("An error occured.", MSG_ERROR);
        return "";
      }

      return "Uploaded";
    }

    $text  = '<form enctype="multipart/form-data" method="post">';
    $text .= $form->show();
    $text .= '<input type="submit" value="Submit"/>';
    $text .= '</form>';

    return $text;
  }
}
