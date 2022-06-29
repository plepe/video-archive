<?php
class Video extends Entity {
  public static $dbFields = ['id', 'title', 'date', 'filesize'];

  function save ($data, $changeset) {
    global $db;

    parent::save($data, $changeset);

    if ($this->isNew) {
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

  static function list ($options = []) {
    global $db;

    $qry = $db->query('select * from video left join entity on video.id=entity.id');
    while ($elem = $qry->fetch()) {
      yield new Video($elem['id'], $elem);
    }
  }

  static function get ($id, $options = []) {
    global $db;

    $qry = $db->query('select * from video left join entity on video.id=entity.id where video.id=' . $db->quote($id));
    $res = $qry->fetchAll();

    if (sizeof($res)) {
      return new Video($id, $res[0]);
    }

    return null;
  }
}
