#!/usr/bin/env python2
import pam
import fileinput
import sys
import json

lines = list(fileinput.input())

if len(lines) < 2:
    print('Expected only two lines: user, password')
    sys.exit(1)

if pam.authenticate(lines[0].replace('\n', '').strip(), lines[1].replace('\n', '').strip()):
    print('Success')
    sys.exit(0)

print('Error')
sys.exit(1)
