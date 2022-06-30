<?php
function default_access ($type) {
    global $auth;
    global $default_access;

    foreach ($default_access as $user => $rights) {
      if (in_array($type, $rights) && $auth->access($user)) {
        return true;
      }
    }

    return false;
}
