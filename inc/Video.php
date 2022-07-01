<?php
class Video extends Entity {
  public static $dbFields = ['title', 'date', 'originalFile', 'filesize', 'duration'];
  public static $dbTable = 'video';

  function load () {
    global $db;

    if (!$this->isLoaded) {
      $qry = $db->query('select * from video where video.id=' . $db->quote($this->id));
      $res = $qry->fetchAll();

      $this->data = array_merge($this->data, $res[0]);
    }
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
    $result .= "<div class=\"title\"><a href=\"?id={$this->id}&amp;action=show\">{$this->data['title']}</a></div>\n";
    $result .= "</div>";

    return $result;
  }

  function showFull ($options = []) {
    $result  = "<div id=\"{$this->id}\">\n";
    $result .= "<div class=\"videoContainer\"><video controls><source type=\"video/mp4\" src=\"download.php?id={$this->id}&amp;file=video\"></video></div>\n";
    $result .= "<div class=\"title\">{$this->data['title']}</div>\n";
    $result .= "</div>";

    return $result;
  }

  function processCreate ($options, $changeset) {
    global $data_dir;
    $recode = false;

    $data = [];

    $originalFile = "{$data_dir}/{$this->fileName('original')}";
    $videoFile = "{$data_dir}/{$this->fileName('video')}";

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
      $cmd = "ln -s original.mp4 " . escapeshellarg($videoFile);
    }
    system($cmd);

    $this->save($data, $changeset);
  }
}
