<?php
class Video extends Entity {
  public static $dbFields = ['id', 'title', 'date', 'filesize'];

  function load () {
    global $db;

    if (!$this->isLoaded) {
      $qry = $db->query('select * from video where video.id=' . $db->quote($this->id));
      $res = $qry->fetchAll();

      $this->data = array_merge($this->data, $res[0]);
    }
  }

  function save ($data, $changeset) {
    global $db;
    $isNew = $this->isNew;

    parent::save($data, $changeset);

    if ($isNew) {
      $f = [$db->quoteIdent('id')];
      $str = [$db->quote($this->id)];
      foreach ($this::$dbFields as $field) {
        if (array_key_exists($field, $data)) {
          $f[] = $db->quoteIdent($field);
          $str[] = $db->quote($data[$field]);
        }
      }

      $db->query('insert into video (' . implode(', ', $f) . ') values (' . implode(', ', $str) . ')');
    } else {
      $str = [];
      foreach ($this::$dbFields as $field) {
        if (array_key_exists($field, $data)) {
          $str[] = $db->quoteIdent($field) . '=' . $db->quote($data[$field]);
        }
      }

      $db->query('update video set ' . implode(', ', $str) . ' where id=' . $db->quote($this->id));
    }
  }

  function fileName ($fileId, $options = []) {
    if ($fileId === 'video') {
      return "{$this->id}/video.mp4";
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
}
