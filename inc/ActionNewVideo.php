<?php
class ActionNewVideo {
  function show () {
    global $data_dir;
    $id = generateId();

    $form = new form('data', [
      'title'   => [
        'type'    => 'text',
        'name'    => 'Title',
      ],
      'file'    => [
        'type'    => 'file',
        'name'    => 'Video file',
        'path'    => "{$data_dir}/{$id}",
        'template' => 'video.[ext]',
        'req'     => true,
      ],
      'date'    => [
        'type'    => 'datetime',
        'name'    => 'Date',
      ],
    ]);

    if ($form->is_complete()) {
      mkdir("{$data_dir}/{$id}");
      $data = $form->save_data();

      return "Uploaded";
    }

    $text  = '<form enctype="multipart/form-data" method="post">';
    $text .= $form->show();
    $text .= '<input type="submit" value="Submit"/>';
    $text .= '</form>';

    return $text;
  }
}
