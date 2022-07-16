<?php
class Video extends Entity {
  public static $dbFields = ['title', 'date', 'originalFile', 'filesize', 'duration'];
  public static $dbTable = 'video';

  function _load () {
    global $db;

    $qry = $db->query('select * from video where id=' . $db->quote($this->id));
    $res = $qry->fetchAll();
    $this->data = array_merge($this->data, $res[0]);
  }

  function formEdit () {
    $result = parent::formEdit();

    return array_merge($result, [
      'date'    => [
        'type'    => 'datetime',
        'name'    => 'Date',
      ],
    ]);
  }

  function fileName ($fileId, $options = []) {
    if ($fileId === 'video') {
      return "{$this->id}/video.mp4";
    }
    if ($fileId === 'original') {
      return "{$this->id}/{$this->data['originalFile']}";
    }
    if ($fileId === 'thumbnail') {
      return "{$this->id}/thumbnail.jpg";
    }
  }

  function showTeaser ($options = []) {
    $classAdd = '';
    if ($options['current'] === $this->id) {
      $classAdd .= ' current';
    }

    $result  = "<div id=\"{$this->id}\" class=\"{$classAdd}\">\n";
    $result .= "<div class=\"videoContainer\"></div>\n";

    $url = $options['additionalUrlParameters'] ?? [];
    $url = array_merge($url, [ 'id' => $this->id, 'action' => 'show' ]);

    $result .= "<div class=\"title\"><a href=\"" . htmlentities(url($url)) . "\">{$this->data['title']}</a></div>\n";
    $result .= "</div>";

    return $result;
  }

  function showFull ($options = []) {
    $result  = "<div id=\"{$this->id}\">\n";

    $url = [ 'id' => $this->id, 'file' => 'video' ];
    $url = url($url, 'download.php');

    $result .= "<div class=\"videoContainer\"><video class='video-js' data-setup='{}' controls><source type=\"video/mp4\" src=\"{$url}\"></video></div>\n";
    $result .= "<div class=\"title\">{$this->data['title']}</div>\n";

    if (array_key_exists('playlist', $options)) {
      $result .= '<div class="playlist">';
      $playlist = Entity::get($options['playlist']);
      if ($playlist) {
        $result .= $playlist->showFull([ 'current' => $this->id ]);
      }
      $result .= '</div>';
    }

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

    $this->processUpdateThumbnail($options, $changeset);

    $data['ready'] = true;
    $this->save($data, $changeset);
  }

  function processUpdateThumbnail ($options, $changeset) {
    global $data_dir;
    $originalFile = "{$data_dir}/{$this->fileName('original')}";
    $thumbFile = "{$data_dir}/{$this->fileName('thumbnail')}";

    $cmd = "ffmpeg -ss 10 -i " . escapeshellarg($originalFile) . " -vframes 1 -q:v 2 " . escapeshellarg($thumbFile);
    system($cmd);
  }

  function remove () {
    global $data_dir;

    $result = delTree("{$data_dir}{$this->id}");
    if (!$result) {
      return false;
    }

    return parent::remove();
  }
}
