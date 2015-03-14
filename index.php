<?php
$userAgent = $_SERVER['HTTP_USER_AGENT'];
$DEVICE_TYPE="";
if (stristr($userAgent, "Mobile")) {
    $DEVICE_TYPE="MOBILE";
}
?>
<html>
<head>

<script type="text/javascript">
function openUrl(url, onready) {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.open("GET", url, true);
    if (onready) {
        xmlhttp.onreadystatechange=function() {
            if (xmlhttp.readyState==4 && xmlhttp.status==200) {
                if (xmlhttp.responseText) {
                    onready(xmlhttp.responseText);
                }
            }
        }
    }
    xmlhttp.send();
}

function openProject(projectName, script, path) {
    var url = "open.php?project=" + encodeURIComponent(projectName);
    if (script) {
        url += "&script=" + encodeURIComponent(script);
    }
    if (path) {
        url += "&path=" + encodeURIComponent(path);
    }
    openUrl(url, alert);
}

function addProject() {
    var name = document.getElementById('newName').value;
    var project = document.getElementById('newProject').value;
    var server = document.getElementById('newServer').value;
    var user = document.getElementById('newUser').value;
    var directory = document.getElementById('newDirectory').value;
    openUrl("add.php?name=" + name + "&project=" + project + "&server=" + server + "&user=" + user + "&directory=" + directory, loadProjects);
}

function deleteProject(projectName) {
    openUrl("delete.php?project=" + projectName, loadProjects);
}

var timeoutId = 0;
var selectedItemId = -1;
var selectedItem = null;
function loadProjects(filter) {
    clearTimeout(timeoutId);
    var url = "projects.php"
    if (filter) {
        url += "?filter=" + filter
    }
    timeoutId = setTimeout(function() {
        openUrl(url, function(data) {
            document.getElementById('projects').innerHTML = data;
            selectedItemId = -1;
            selectNext();
        });
    }, 100);
}

function selectNext() {
    if (selectedItem) {
        selectedItem.className = "item";
    }
    var items = document.getElementsByClassName('item');
    selectedItem = items[++selectedItemId];
    if (selectedItem) {
        selectedItem.className = "item selected";
    }
}

function selectPrevious() {
    if (selectedItem) {
        selectedItem.className = "item";
    }
    var items = document.getElementsByClassName('item');
    selectedItem = items[--selectedItemId];
    if (selectedItem) {
        selectedItem.className = "item selected";
    }
}

function keyAction(e) {
    console.log(e);
    if (e.keyCode == 191) { // '/'
        console.log("/");
        document.getElementById('filter').focus();
    } else if (e.keyCode == 27) { // 'ESC'
        console.log("ESC");
        document.getElementById('filter').value = "";
        loadProjects();
    } else if (e.keyCode == 13) { // 'ENTER'
        if (selectedItem) {
            eval(selectedItem.href);
        }
    } else if (e.keyCode == 39) { // '->'
        document.getElementById('filter').blur();
        selectNext();
    } else if (e.keyCode == 37) { // '<-'
        document.getElementById('filter').blur();
        selectPrevious();
    }
}

document.onkeyup = keyAction;
</script>
<link rel="stylesheet" href="stylesheet.css"/>

<?php
if ($DEVICE_TYPE == "MOBILE") {
    ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0,  minimum-scale=1.0">
    <meta name="apple-mobile-web-app-title" content="Sublime"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="mobile-web-app-capable" content="yes"/>
    <link rel="apple-touch-icon" href="logo-sublime-3.png"/>
    <?php
}
?>
</head>
<body onload="loadProjects()">
    <h1>Projects</h1>

    Filter: <input type="text" id="filter" onkeyup="loadProjects(this.value)" />

    <div id="projects"></div>

    <div id="addProject">
        <h3>Add project</h3>
        <dl>
            <dt>Name</dt>
            <dd><input type="text" id='newName'/></dd>
            <dt>Project</dt>
            <dd><input type="text" id='newProject'/></dd>
            <dt>Server</dt>
            <dd><input type="text" id='newServer'/></dd>
            <dt>User</dt>
            <dd><input type="text" id='newUser'/></dd>
            <dt>Directory</dt>
            <dd><input type="text" id='newDirectory'/></dd>
            <dt><input type="button" value="Save" onclick="addProject();" /></dt>
        </dl>
    </div>
</body>
</html>