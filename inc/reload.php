<?php
function reload ($url=null) {
  messages_keep();

  if($url === null)
    $url = $_SERVER['REQUEST_URI'];

  Header("Location: $url");
}
