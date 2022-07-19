<?php
function generateId($length = 16) {
  $id = bin2hex(random_bytes($length / 2));
  return $id;
}
