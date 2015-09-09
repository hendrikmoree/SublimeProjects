## begin license ##
#
# All rights reserved.
#
# Copyright (C) 2015 Seecr (Seek You Too B.V.) http://seecr.nl
#
## end license ##

<?php
$name = $_GET["name"];
$project = $_GET["project"];
$server = $_GET["server"];
$user = $_GET["user"];
$directory = $_GET["directory"];
$port = $_GET["port"];

$f = fopen("config.json", "r");
$config = json_decode(fread($f, filesize("config.json")));
fclose($f);

$config->{$name} = array(
        'project' => $project,
        'server' => $server,
        'user' => $user,
        'directory' => $directory,
        'port' => $port
    );

$f = fopen("config.json", "w");
fwrite($f, json_encode($config));
fclose($f);

?>