<?php
$project = $_GET['project'];

$f = fopen("config.json", "r");
$config = json_decode(fread($f, filesize("config.json")));
fclose($f);

unset($config->{$project});

$f = fopen("config.json", "w");
fwrite($f, json_encode($config));
fclose($f);

?>
