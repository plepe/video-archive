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
