<?php
## begin license ##
#
# All rights reserved.
#
# Copyright (C) 2015-2016 Seecr (Seek You Too B.V.) http://seecr.nl
#
## end license ##

$searchProject = (isset($_GET['searchProject']) ? $_GET['searchProject'] : null);
if ($searchProject) {
    $splitted = split(" - ", $searchProject, 2);
    $project = $splitted[0];
    $server = $splitted[1];

    $f = fopen("projects.json", "r");
    $projectsDict = json_decode(fread($f, filesize("projects.json")), true);
    fclose($f);
    $script = $projectsDict[$server]["script"];
    $path = null;
    if (in_array("path", $projectsDict[$server])) {
        $path = $projectsDict[$server]["path"];
    }
} else {
    $project = (isset($_GET['project']) ? $_GET['project'] : null);
    $script = (isset($_GET['script']) ? $_GET['script'] : null);
    $path = (isset($_GET['path']) ? $_GET['path'] : null);
}

$command = "HOME=/Users/hendrik sudo -u Hendrik ";
if ($script != "sublime") {
    $command .= "/Users/hendrik/Development/sublime_packages/sublime-git/bin/";
}

if ($script) {
    $command .= $script;
    if ($path) {
        $command .= " \"$path/$project\"";
    } else {
        $command .= " \"$project\"";
    }
} else {
    $f = fopen("config.json", "r");
    $config = json_decode(fread($f, filesize("config.json")));
    fclose($f);

    $project = $config->{$project};
    $port = "";
    if (array_key_exists('port', $project)) {
        $port = $project->{'port'};
    }
    $command .= "sublime-project ".$project->{'project'}." ".$project->{'server'}." ".$project->{'user'}." ".$project->{'directory'}." ".$port;
}

$PATH = (isset($_ENV["PATH"]) ? $_ENV["PATH"] : null);
putenv("PATH=" .$PATH. ':/usr/bin:/usr/sbin:/usr/local/bin:/bin/:/sbin:/Users/hendrik/Development/sublime_packages/sublime-git/bin');
exec($command." 2>&1", $ouput, $exitcode);
header("Content-Type: text/plain");
if ($exitcode != 0) {
    foreach ($ouput as $x) {
        echo $x."\n";
    }
}
?>