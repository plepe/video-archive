<?php
function process_queue_next () {
  global $db;

  $res = $db->query('select * from process_queue where start is null order by submit asc limit 1');
  $elem = $res->fetch();
  $res->closeCursor();

  if (!$elem) {
    return false;
  }

  $proc_id = $elem['proc_id'];
  $db->query('update process_queue set start=now() where proc_id=' . $db->quote($proc_id));
  print "Process {$proc_id} ...";

  $changeset = new Changeset('process queue');
  $changeset->open();
  $entity = Entity::get($elem['id']);
  $result = call_user_func([$entity, 'process' . $elem['func']], json_decode($elem['options'], 1), $changeset);

  $db->query('update process_queue set end=now(), result=' . $db->quote(json_encode($result)) . ' where proc_id=' . $db->quote($proc_id));
  $changeset->commit();
  print " done!\n";

  return true;
}
