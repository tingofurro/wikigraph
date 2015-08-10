#!/usr/bin/python
import shutil

from dbco import *
from s1Build import createGraph
from s2Community import buildCommunity
from s3Nlp import useNLP

limit = 400

db_prefix = ''
summaryFolder = '../../crawler/summary'

if len(sys.argv) > 2 and sys.argv[1] == 'reset':
	shall = raw_input("Sure you want to reset DB? (y/N) ").lower() == 'y'
	if shall:
		cur.execute("TRUNCATE TABLE `"+db_prefix+"cluster`")
		cur.execute("UPDATE "+db_prefix+"page SET cluster1=0, cluster2=0, cluster3=0, cluster4=0, cluster5=0")

level = 0; cluster = 0; # we have to build the first one.

print "Level: ", level, ". Cluster:", cluster

createGraph(limit, level, cluster, db_prefix)
print "Built graph for community detection"

buildCommunity()
print "Ran community detection"

useNLP(summaryFolder)
print "Reassign nodes with NLP + name communities"