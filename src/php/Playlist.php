<?php
class Playlist extends Entity {
  public static $dbFields = ['title'];
  public static $dbTable = 'playlist';

  function _load () {
    global $db;

    $qry = $db->query('select * from playlist where id=' . $db->quote($this->id));
    $res = $qry->fetchAll();
    $this->data = array_merge($this->data, $res[0]);

    $qry = $db->query('select video_id from playlist_video where playlist_id=' . $db->quote($this->id) . ' order by weight asc');
    $this->data['videos'] = array_map(function ($elem) {
      return $elem['video_id'];
    }, $qry->fetchAll());
  }

  function save ($data, $changeset) {
    global $db;

    parent::save($data, $changeset);

    $db->query(dbCompileRemove('playlist_video', ['playlist_id' => $this->id]));
    $res->closeCursor();

    foreach ($data['videos'] as $index => $id) {
      $res = $db->query(dbCompileInsert('playlist_video',
        ['playlist_id' => $this->id, 'video_id' => $id, 'weight' => $index]
      ));
      $res->closeCursor();

    }

    return true;
  }

  function remove () {
    global $db;
    $db->query(dbCompileRemove('playlist_video', ['playlist_id' => $this->id]));
    return parent::remove();
  }

  function fileName ($fileId, $options = []) {
    if ($fileId === 'video') {
      return "{$this->id}/video.mp4";
    }
    if ($fileId === 'original') {
      return "{$this->id}/{$this->data['originalFile']}";
    }
  }

  function showTeaser ($options = []) {
    $result  = "<div id=\"{$this->id}\">\n";
    $result .= "<div class=\"videoContainer\"></div>\n";

    $url = $options['additionalUrlParameters'] ?? [];
    $url = array_merge($url, [ 'id' => $this->id, 'action' => 'show' ]);

    $result .= "<div class=\"title\"><a href=\"" . htmlentities(url($url)) . "\">{$this->data['title']}</a></div>\n";
    $result .= "</div>";

    return $result;
  }

  function showFull ($options = []) {
    $result  = "<div id=\"{$this->id}\">\n";
    $result .= '<div class="playlist-content">';

    $memberOptions = $options;
    if (array_key_exists('additionalUrlParameters', $memberOptions)) {
      $memberOptions['additionalUrlParameters']['playlist'] = $this->id;
    }
    else {
      $memberOptions['additionalUrlParameters'] = [ 'playlist' => $this->id ];
    }

    foreach ($this->data['videos'] as $memberId) {
      $member = Entity::get($memberId);
      $result .= $member->showTeaser($memberOptions);
    }

    $result .= '</div>';
    $result .= "</div>";

    return $result;
  }

  function processCreate ($options, $changeset) {
    global $data_dir;
    $recode = false;
    $vcodec = 'copy';
    $acodec = 'copy';

    $data = [];

    $originalFile = "{$data_dir}/{$this->fileName('original')}";
    $videoFile = "{$data_dir}/{$this->fileName('video')}";

    if (!preg_match('/\.mp4$/i', $originalFile)) {
      $recode = true;
    }

    exec("ffprobe " . escapeshellarg($originalFile) . " 2>&1", $output);
    foreach ($output as $r) {
      if (preg_match("/^  Duration: (\d+):(\d+):(\d+)\.(\d+),/", $r, $m)) {
        $data['duration'] = (int)$m[1] * 3600 + (int)$m[2] * 60 + (int)$m[3] + (int)$m[4] / 100;
      }

      if (preg_match('/^    Stream.*Video: ([a-zA-Z0-9]+) [^,]+, ([a-zA-Z0-9]+)/', $r, $m)) {
        if ($m[1] !== 'h264' || $m[2] !== 'yuv420p') {
          $vcodec = 'h264 -pix_fmt yuv420p';
          $recode = true;
        }
      }

      if (preg_match('/^    Stream.*Audio: ([a-zA-Z0-9]+)/', $r, $m)) {
        if ($m[1] !== 'aac') {
          $acodec = 'aac';
          $recode = true;
        }
      }
    }

    if ($recode) {
      $cmd = "ffmpeg -i " . escapeshellarg($originalFile) . " -vcodec {$vcodec} -acodec {$acodec} -strict -2 -y " . escapeshellarg($videoFile);
    }
    else {
      $cmd = "ln -s " . escapeshellarg($originalFile) . " " . escapeshellarg($videoFile);
    }
    print "Running command: {$cmd}\n";
    system($cmd);

    $data['ready'] = true;
    $this->save($data, $changeset);
  }

  function formEdit () {
    $result = parent::formEdit();

    $video_list = [];
    foreach (Video::list() as $video) {
      $video_list[$video->id] = $video->data['title'];
    }

    return array_merge($result, [
      'videos'    => [
        'type'    => 'autocomplete',
        'name'    => 'List of Videos',
        'count'   => ['default' => 1, 'index_type' => 'array'],
        'values'  => $video_list,
      ],
    ]);
  }
}
