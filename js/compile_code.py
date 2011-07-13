from __future__ import with_statement
from sys import argv as ARGV
import os

cur_dir = ARGV[1]
output = ARGV[2]

prov_regex = re.compile('goog\.provide\s*\(\s*[\'\"]([^\)]+)[\'\"]\s*\)')
hashes = {}

def make_hashfile():
    with open("hashfile.txt") as f:
        for here, dirs, paths in os.walk(cur_dir):
            for path in paths:
                if path.endswith('.js'):
                    with open(os.path.join(here, path)) as jsfile:
                        for line in jsfile:
                            match = prov_regex.search(line)
                            if match:
                                hashes[match.group(1)] = here
                                f.write("%s\t%s\n" % (match.group(1), here))

def send_to_compiler():

