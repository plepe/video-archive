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
      $playlist->save($data, $changeset);
      $changeset->commit();

      return "Saved";
    }

    $text  = '<form enctype="multipart/form-data" method="post">';
    $text .= $form->show();
    $text .= '<input type="submit" value="Submit"/>';
    $text .= '</form>';

    return $text;
  }
}
