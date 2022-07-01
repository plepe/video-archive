<?php include "conf.php"; /* load a local configuration */ ?>
<?php include "modulekit/loader.php"; /* loads all php-includes */ ?>
<?php session_start(); ?>
<?php call_hooks("init"); /* initialize submodules */ ?>
<?php
$current_user = $auth->current_user();

$db = new PDOext($db_conf);
$db->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES utf8");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);


$id = $_REQUEST['id'] ?? null;
$actionId = $_REQUEST['action'] ?? ($id ? 'show' : 'list');

$actionClass = "Action" . ucfirst($actionId);
$action = new $actionClass($id);

if ($action->access()) {
  $content = $action->show();
}
else {
  $content = 'Access denied';
}

$main_menu = [];

$main_menu[] = [
  'url' => '.',
  'text' => 'Home',
];

if ($auth->is_logged_in()) {
  $main_menu[] = [
    'url' => '?action=logout',
    'text' => 'Logout',
  ];
} else {
  $main_menu[] = [
    'url' => '?action=login',
    'text' => 'Login',
  ];
}

if (method_exists($action, 'menu')) {
  $main_menu = array_merge($main_menu, $action->menu());
}

?>
<!DOCTYPE HTML>
<html>
<head>
<?php print modulekit_include_js(); /* prints all js-includes */ ?>
<?php print modulekit_include_css(); /* prints all css-includes */ ?>
<?php print_add_html_headers(); ?>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel='stylesheet' type='text/css' href='demo.css'/>
</head>
<body>
<?php
print $content;
?>

<div id='main-menu'>
<ul>
<?php
foreach ($main_menu as $link) {
  print '<li><a href="' . htmlentities($link['url']) . '">' . htmlentities($link['text']) . '</a></li>';
}
?>
</ul>
</div>

</body>
</html>
