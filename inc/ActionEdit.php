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

    $form = new form('data', [
      'title'   => [
        'type'    => 'text',
        'name'    => 'Title',
      ],
      'file'    => [
        'type'    => 'file',
        'name'    => 'Video file',
        'path'    => "{$data_dir}/{$this->entity->id}",
        'web_path' => "download.php?id={$this->id}&file=video",
        'template' => 'video.[ext]',
        'req'     => true,
      ],
      'date'    => [
        'type'    => 'datetime',
        'name'    => 'Date',
      ],
    ]);

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
      $data['filesize'] = $data['file']['size'];

      $changeset = new Changeset('new video');
      $changeset->open();
      $this->entity->save($data, $changeset);
      $changeset->commit();

      return "Uploaded";
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
        'url' => "?id={$this->id}&action=show",
        'text' => 'view',
      ];
    }

    return $result;
  }
}
