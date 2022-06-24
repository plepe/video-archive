<?php
class ActionEdit {
  function __construct ($id) {
    $this->id = $id;
  }

  function show () {
    global $data_dir;
    $video = Video::get($this->id);

    $form = new form('data', [
      'title'   => [
        'type'    => 'text',
        'name'    => 'Title',
      ],
      'file'    => [
        'type'    => 'file',
        'name'    => 'Video file',
        'path'    => "{$data_dir}/{$video->id}",
        'web_path' => "download.php?id={$this->id}&file=video",
        'template' => 'video.[ext]',
        'req'     => true,
      ],
      'date'    => [
        'type'    => 'datetime',
        'name'    => 'Date',
      ],
    ]);

    $data = $video->data;
    $data['file'] = [
      'name' => 'video.mp4',
      'type' => 'video/mp4',
      'size' => $data['filesize'],
    ];

    if ($form->is_empty()) {
      $form->set_data($data);
    }

    if ($form->is_complete()) {
      mkdir("{$data_dir}/{$video->id}");
      $data = $form->save_data();
      $data['filesize'] = $data['file']['size'];

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
