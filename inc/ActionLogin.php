<?php
class ActionLogin {
  function __construct ($id) {
    global $auth;

    $this->form = new AuthPages($auth);

    if ($auth->is_logged_in()) {
      if($_REQUEST['return'])
        header("Location: {$_REQUEST['return']}");
      else
        header("Location: .");
    }
  }

  function access () {
    return true;
  }

  function show ($options = []) {
    return $this->form->show_form();
  }
}
