<?php
function url ($options, $file = '') {
  if (is_string($options)) {
    return $options;
  }

  if (!sizeof($options)) {
    return '.';
  }

  return $file . '?' . http_build_query($options);
}
