<?php
class ActionNewVideo {
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
        'template' => 'video.[ext]',
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
      $data['filesize'] = $data['file']['size'];
print_r($data);

      $changeset = new Changeset('new video');
      $changeset->open();
      $video->save($data, $changeset);
      $changeset->commit();

      return "Uploaded";
    }

    $text  = '<form enctype="multipart/form-data" method="post">';
    $text .= $form->show();
    $text .= '<input type="submit" value="Submit"/>';
    $text .= '</form>';

    return $text;
  }
}
