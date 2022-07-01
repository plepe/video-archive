<?php
function dbCompileInsert ($table, $values) {
  global $db;

  $compiledKeys = [];
  $compiledValues = [];

  foreach ($values as $k => $v) {
    $compiledKeys[] = $db->quoteIdent($k);
    $compiledValues[] = $db->quote($v);
  }

  return 'insert into ' . $db->quoteIdent($table) . '(' . implode(', ', $compiledKeys) . ') values (' . implode(', ', $compiledValues) . ')';
}

function dbCompileUpdate ($table, $values, $where) {
  global $db;

  if (!sizeof($values) ) {
    return null;
  }

  $compiledEntries = [];
  $compiledWhere = [];

  foreach ($values as $k => $v) {
    $compiledEntries[] = $db->quoteIdent($k) . '=' . $db->quote($v);
  }

  foreach ($where as $k => $v) {
    if ($v === null) {
      $compiledWhere[] = $db->quoteIdent($k) . ' is null';
    } else {
      $compiledWhere[] = $db->quoteIdent($k) . '=' . $db->quote($v);
    }
  }

  return 'update ' . $db->quoteIdent($table) . ' set ' . implode(', ', $compiledEntries) . ' where ' . implode(' and ', $compiledWhere);
}
