<?php include "conf.php"; /* load a local configuration */ ?>
<?php include "modulekit/loader.php"; /* loads all php-includes */ ?>
<?php call_hooks("init"); /* initialize submodules */ ?>
<?php
$db = new PDOext($db_conf);

$action_id = $_REQUEST['action'] ?? 'list';
$id = $_REQUEST['id'] ?? null;

$action_class = "Action" . ucfirst($action_id);
$action = new $action_class($id);
$content = $action->show();

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
</body>
</html>
