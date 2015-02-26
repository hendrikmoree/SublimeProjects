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
exec("HOME=/Users/hendrik sudo -u Hendrik ssh development \"ls development\" | grep \"$filter\" 2>&1", $dev_vm, $exitcode);
exec("HOME=/Users/hendrik sudo -u Hendrik $(seecr-login zp development --print) \"ls -1 . | grep story\"  | grep \"$filter\" 2>&1", $zp_dev, $exitcode);

$cart = array(
        'Dev servers' => array('projects' => $projects, 'script' => ""),
        'Development VM' => array('projects' => $dev_vm, 'script' => "sublime-project"),
        'ZP_dev' => array('projects' => $zp_dev, 'script' => "zp-sublime-project")
    );

if ($json) {
    header('Content-Type: application/json');
    echo "{";
    $started = FALSE;
    foreach ($cart as $name => $projects) {
        if ($started) {
            echo ",";
        }
        $started = TRUE;
        echo '"'.$name.'": { "script": "'.$projects['script'].'", "projects" : [';
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

foreach ($cart as $name => $projects) {
    $started = FALSE;
    foreach ($projects['projects'] as $project) {
        if (!$started) { ?>
            <h3><?php echo $name?></h3>
            <div class="projects">
            <ul><?php
            $started = TRUE;
        }
        ?><li><a class="item" href="javascript: openProject('<?php echo $project?>', '<?php echo $projects['script']?>');"><?php echo $project?></a><!-- &nbsp;<a href="javascript: deleteProject('<?php echo $project?>');">delete</a>--></li><?php
    }

    if ($started) {
        ?></ul></div><?php
    }
}
?>