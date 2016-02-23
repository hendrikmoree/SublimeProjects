<?php
## begin license ##
#
# All rights reserved.
#
# Copyright (C) 2016 Seecr (Seek You Too B.V.) http://seecr.nl
#
## end license ##
$filter = (isset($_GET['filter']) ? $_GET['filter'] : null);
$json = (isset($_GET['json']) ? $_GET['json'] : null);
$opensearch = (isset($_GET['opensearch']) ? $_GET['opensearch'] : null);
$forceCache = (isset($_GET['forceCache']) ? $_GET['forceCache'] : null);
$f = fopen("config.json", "r");
$config = json_decode(fread($f, filesize("config.json")));
fclose($f);

$projects = array();
foreach ($config as $name => $project) {
    if (!$filter || ($filter && stripos($name, $filter) !== FALSE)) {
        $projects[] = $name;
    }
}

$PROJECTS_FILE = "projects.json";
$f = fopen($PROJECTS_FILE, "r");
$projectsDict = json_decode(fread($f, filesize($PROJECTS_FILE)), true);
fclose($f);

if ($opensearch) {
    header("Content-Type: application/x-suggestions+json");
    header("Access-Control-Allow-Headers: X-Requested-With");
    echo "[";
    echo "\"$filter\",";
    echo "[";
    $started = FALSE;
    foreach ($projectsDict as $name => $projects) {
        foreach ($projects['projects'] as $project) {
            if ($filter and stripos($project, $filter) === FALSE) {
                continue;
            }
            if ($started) {
                echo ",";
            }
            $started = TRUE;
            echo "\"$project - $name\"";
        }
    }
    echo "]";
    echo "]";
    die(0);
}

if ($json) {
    header('Content-Type: application/json');
    echo "{";
    $started = FALSE;
    foreach ($projectsDict as $name => $projects) {
        if ($started) {
            echo ",";
        }
        $started = TRUE;
        echo '"'.$name.'": { "script": "'.$projects['script'].'", ';
        if (array_key_exists("path", $projects)) {
            echo '"path": "'.$projects['path'].'", ';
        }
        echo '"projects" : [';
        $started2 = FALSE;
        foreach ($projects['projects'] as $project) {
            if ($filter and stripos($project, $filter) == FALSE) {
                continue;
            }
            if ($started2) {
                echo ",";
            }
            $started2 = TRUE;
            echo '"'.$project.'"';
        }
        echo ']}';
    }
    echo "}";
    die(0);
}

foreach ($projectsDict as $name => $projects) {
    $started = FALSE;
    foreach ($projects['projects'] as $project) {
        if ($filter and stripos($project, $filter) == FALSE) {
            continue;
        }
        if (!$started) { ?>
            <h3><?php echo $name?></h3>
            <div class="projects">
            <ul><?php
            $started = TRUE;
        }
        $openProject = "openProject('".$project."', '".$projects['script']."'";
        if (array_key_exists("path", $projects)) {
            $openProject .= ", '".$projects['path']."'";
        }
        $openProject .= ");";

        ?><li><a class="item" href="javascript: <?php echo $openProject?>"><?php echo $project?></a><!-- &nbsp;<a href="javascript: deleteProject('<?php echo $project?>');">delete</a>--></li><?php
    }

    if ($started) {
        ?></ul></div><?php
    }
}
?>