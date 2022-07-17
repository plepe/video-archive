<?php include "conf.php"; /* load a local configuration */ ?>
<?php include "modulekit/loader.php"; /* loads all php-includes */ ?>
<?php session_start(); ?>
<?php call_hooks("init"); /* initialize submodules */ ?>
<?php
$db_conf['debug'] = 0;
$db = new PDOext($db_conf);
$db->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES utf8");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$id = $_REQUEST['id'];

$entity = Entity::get($id);

if (!$entity->access('view')) {
  Header("HTTP/1.1 403 Forbidden");
  print "Access denied.";
  exit(0);
}

session_write_close();

Header("Content-type: application/json");
print json_encode($entity->data);
