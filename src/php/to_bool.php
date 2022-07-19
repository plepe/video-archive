<?php
function to_bool ($val) {
  return $val === null ? null : (boolean)$val;
}
