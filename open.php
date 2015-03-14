<?php
$project = (isset($_GET['project']) ? $_GET['project'] : null);
$script = (isset($_GET['script']) ? $_GET['script'] : null);
$path = (isset($_GET['path']) ? $_GET['path'] : null);

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
    $command .= "sublime-project ".$project->{'project'}." ".$project->{'server'}." ".$project->{'user'}." ".$project->{'directory'};
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