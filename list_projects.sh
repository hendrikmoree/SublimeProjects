#!/bin/bash
## begin license ##
#
# All rights reserved.
#
# Copyright (C) 2016 Seecr (Seek You Too B.V.) http://seecr.nl
#
## end license ##

export HOME=/Users/hendrik
export PATH="$PATH:/bin:/usr/bin:/usr/local/bin"
GREP_DIRS="find . -maxdepth 1 -type d | sed 's,./,,' | grep -v '^\.' | tr '\n' ','"
sudo -u Hendrik ssh development "ls -1 development | tr '\n' ','" 2>&1
echo ""
sudo -u Hendrik $(seecr-login zp development --print) "$GREP_DIRS" 2>&1
echo ""
sudo -u Hendrik $(seecr-login drenthe development --print) "$GREP_DIRS" 2>&1
echo ""
sudo -u Hendrik $(seecr-login edurep development --print) "$GREP_DIRS" 2>&1
echo ""
sudo -u Hendrik $(seecr-login obkapi development --print) "$GREP_DIRS" 2>&1
echo ""
sudo -u Hendrik ls -1 "/Users/hendrik/Library/Application Support/Sublime Text 3/Packages" | tr '\n' ',' 2>&1