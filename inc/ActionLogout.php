<?php
class ActionLogout {
  function __construct ($id) {
    global $auth;

    $auth->clear_authentication();

    if($_REQUEST['return'])
      header("Location: {$_REQUEST['return']}");
    else
      header("Location: .");
  }

  function show ($options = []) {
    return 'Logged out.';
  }
}
