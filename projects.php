<?php
$filter = (isset($_GET['filter']) ? $_GET['filter'] : null);
$json = (isset($_GET['json']) ? $_GET['json'] : null);
$f = fopen("config.json", "r");
$config = json_decode(fread($f, filesize("config.json")));
fclose($f);

$projects = array();
foreach ($config as $name => $project) {
    if (!$filter || ($filter && stripos($name, $filter) !== FALSE)) {
        $projects[] = $name;
    }
}

$PATH = (isset($_ENV["PATH"]) ? $_ENV["PATH"] : null);
putenv("PATH=" .$PATH. ':/bin:/usr/bin:/usr/local/bin');
exec("HOME=/Users/hendrik sudo -u Hendrik ssh development \"ls development\" | grep -i \"$filter\" 2>&1", $dev_vm, $exitcode);
exec("HOME=/Users/hendrik sudo -u Hendrik $(seecr-login zp development --print) \"ls -1 . | grep story\"  | grep -i \"$filter\" 2>&1", $zp_dev, $exitcode);
exec("HOME=/Users/hendrik sudo -u Hendrik ls \"/Users/hendrik/Library/Application Support/Sublime Text 3/Packages\" | grep -i \"$filter\" 2>&1", $sublime_packages, $exitcode);

$projectsDict = array(
        'Dev servers' => array('projects' => $projects, 'script' => ""),
        'Development VM' => array('projects' => $dev_vm, 'script' => "sublime-project"),
        'ZP_dev' => array('projects' => $zp_dev, 'script' => "zp-sublime-project"),
        'Sublime packages' => array('projects' => $sublime_packages, 'script' => "sublime", 'path' => "/Users/hendrik/Library/Application Support/Sublime Text 3/Packages"),
    );

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