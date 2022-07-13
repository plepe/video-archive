<?php
class ActionNewPlaylist {
  function access () {
    return default_access('create');
  }

  function show () {
    global $data_dir;
    $playlist = new Playlist();

    $video_list = [];
    foreach (Video::list() as $video) {
      $video_list[$video->id] = $video->data['title'];
    }

    $form = new form('data', [
      'title'   => [
        'type'    => 'text',
        'name'    => 'Title',
      ],
      'videos'    => [
        'type'    => 'autocomplete',
        'name'    => 'List of Videos',
        'count'   => ['default' => 1, 'index_type' => 'array'],
        'values'  => $video_list,
      ],
    ]);

    if ($form->is_complete()) {
      $data = $form->save_data();

      $changeset = new Changeset('new playlist');
      $changeset->open();
      $result = $playlist->save($data, $changeset);
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
