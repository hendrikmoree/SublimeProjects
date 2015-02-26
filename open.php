<?php
$project = $_GET['project'];
$script = $_GET['script'];

$command = "HOME=/Users/hendrik sudo -u Hendrik /Users/hendrik/Development/sublime_packages/sublime-git/bin/";

if ($script) {
    $command .= "$script $project";
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