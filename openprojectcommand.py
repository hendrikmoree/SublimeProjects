## begin license ##
#
# All rights reserved.
#
# Copyright (C) 2015 Seecr (Seek You Too B.V.) http://seecr.nl
#
## end license ##

from urllib.request import urlopen
from json import loads
from sublime_plugin import WindowCommand
from socket import socket, SHUT_WR
from sublime import set_timeout, error_message
from urllib.parse import urlencode

class OpenProjectCommand(WindowCommand):

    def run(self):
        response = loads(urlopen("http://localhost/projects/projects.php?json=true", timeout=5).read().decode("utf-8"))
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
