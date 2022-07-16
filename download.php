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

$fileId = $_REQUEST['file'] ?? 'video';
$id = $_REQUEST['id'];

$video = Video::get($id);

if (!$video->access('view')) {
  Header("HTTP/1.1 403 Forbidden");
  print "Access denied.";
  exit(0);
}

$fileName = $video->fileName($fileId);

session_write_close();

$fullFileName = "{$data_dir}/{$fileName}";
Header("Content-type: ". mime_content_type($fullFileName));
Header("Content-Length: ". filesize($fullFileName));
Header("Last-Modified: ". gmdate('r', filemtime($fullFileName)));

readfile($fullFileName);
