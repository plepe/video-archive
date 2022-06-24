<?php
class ActionList {
  function show () {
    $list = Video::get();
    print_r($list);
  }
}
