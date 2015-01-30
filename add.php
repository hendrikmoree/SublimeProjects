<?php
$name = $_GET["name"];
$project = $_GET["project"];
$server = $_GET["server"];
$user = $_GET["user"];
$directory = $_GET["directory"];

$f = fopen("config.json", "r");
$config = json_decode(fread($f, filesize("config.json")));
fclose($f);

$config->{$name} = array(
        'project' => $project,
        'server' => $server,
        'user' => $user,
        'directory' => $directory
    );

$f = fopen("config.json", "w");
fwrite($f, json_encode($config));
fclose($f);

?>