#!/usr/bin/env python2
import pam
import fileinput
import sys
import json

lines = list(fileinput.input())

if len(lines) < 2:
    print('Expected only two lines: user, password')
    sys.exit(1)

auth = pam.authenticate if hasattr(pam, 'authenticate') else pam.pam().authenticate

if auth(lines[0].replace('\n', '').strip(), lines[1].replace('\n', '').strip()):
    print('Success')
    sys.exit(0)

print('Error')
sys.exit(1)
