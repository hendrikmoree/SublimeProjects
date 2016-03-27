## begin license ##
#
# All rights reserved.
#
# Copyright (C) 2015-2016 Seecr (Seek You Too B.V.) http://seecr.nl
#
## end license ##

from urllib.request import urlopen
from json import loads, dump, load
from sublime_plugin import WindowCommand
from socket import socket, SHUT_WR
from sublime import set_timeout, error_message, message_dialog
from urllib.parse import urlencode
from SublimeUtils.sublimeutils import executeCommand, projectRoot
from os import makedirs
from os.path import isdir, join, dirname, abspath
from subprocess import call, PIPE, Popen, TimeoutExpired
from threading import Thread

mydir = dirname(abspath(__file__))

class OpenProjectCommand(WindowCommand):

    def run(self):
        response = loads(urlopen("http://localhost/projects/projects.php?json=true", timeout=1).read().decode("utf-8"))
        projects = []
        for server, p in response.items():
            for project in p['projects']:
                projects.append("{0} - {1}".format(project, server))
        self.window.show_quick_panel(projects, lambda i: self.open(projects[i], response) if i != -1 else None)

    def open(self, line, response):
        project, server = line.split(' - ', 1)
        script = response[server]['script']
        path = response[server].get('path')

        arguments = dict(project=project, script=script)
        if path:
            arguments['path'] = path
        sok = socket()
        sok.settimeout(1)
        sok.connect(("localhost", 80))
        request = "GET /projects/open.php?{0} HTTP/1.0\r\n\r\n".format(urlencode(arguments))
        sok.send(request.encode('utf-8'))
        sok.shutdown(SHUT_WR)
        set_timeout(lambda: self.readResponse(sok), 2000)

    def readResponse(self, sok):
        response = ""
        while True:
            r = sok.recv(1024)
            if not r:
                break
            response += r.decode('utf-8')
        header, body = response.split('\r\n\r\n')
        if body:
            error_message(body)
        sok.close()

class CheckoutProjectCommand(WindowCommand):
    def run(self):
        self.view = self.window.active_view()
        projects = executeCommand(self.view, ["seecr-git-clone"], remote=True).split('\n')[1:]
        self.window.show_quick_panel(projects, lambda i: self._checkoutProject(i, projects))

    def _checkoutProject(self, i, projects):
        if i == -1:
            return
        depsDdir = join(projectRoot(self.view), "deps.d")
        isdir(depsDdir) or makedirs(depsDdir)
        executeCommand(view=self.view, args=["seecr-git-clone {}".format(projects[i])], projectCwd="deps.d")

class UpdateProjectsCacheCommand(WindowCommand):
    def run(self):
        self.view = self.window.active_view()
        t = Thread(target=self._do)
        t.start()

    def _do(self):
        for i in range(2):
            p = Popen("./list_projects.sh", stdout=PIPE, stderr=PIPE, cwd=mydir)
            outs, errs = None, None
            for n in range(15):
                self.view.set_status('UpdateProjectsCacheCommand', 'Waiting .. %s' % n)
                try:
                    outs, errs = p.communicate(timeout=1)
                    break
                except (TimeoutExpired):
                    pass
                except (ValueError):
                    break
            if outs and n < 29:
                break
        self.view.erase_status('UpdateProjectsCacheCommand')
        if i == 1 and n == 29:
            message_dialog("Cache update failed")
            return

        devProjects = list(load(open(join(mydir, "config.json"))).keys())

        result = [r.strip(',').split(',') for r in str(outs, 'utf-8').split('\n')]
        with open(join(mydir, "projects.json"), 'w') as f:
            dump({
                'Dev servers': dict(projects=devProjects, script=""),
                'Lokale Development VM': dict(projects=result[0], script="sublime-project"),
                'ZP dev': dict(projects=result[1], script="zp-sublime-project"),
                'Drenthe dev': dict(projects=result[2], script="drenthe-sublime-project"),
                'Edurep dev': dict(projects=result[3], script="edurep-sublime-project"),
                'OBK-Api dev': dict(projects=result[4], script="obkapi-sublime-project"),
                'Sublime packages': dict(projects=result[5], script="sublime", path="/Users/hendrik/Library/Application Support/Sublime Text 3/Packages"),
            }, f, indent=4)
        message_dialog("Cache updated")
