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

  function access () {
    global $auth;

    if ($auth->is_logged_in()) {
      return true;
    }
  }

  function menu () {
    global $auth;

    if ($auth->is_logged_in()) {
      return [
        'url' => [ 'action' => 'logout' ],
        'text' => 'Logout',
      ];
    }
  }

  function show ($options = []) {
    return 'Logged out.';
  }
}
