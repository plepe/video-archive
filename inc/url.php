<?php
function url ($options) {
  if (is_string($options)) {
    return $options;
  }

  if (!sizeof($options)) {
    return '.';
  }

  return '?' . http_build_query($options);
}
