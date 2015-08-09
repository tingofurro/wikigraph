#!/usr/bin/python
import shutil

from dbco import *
from s1Build import createGraph
from s2Community import buildCommunity
from s3Nlp import useNLP
from s4QA import QA
from s5Extrapolate import extrapolate
from s6Save import saveResults

limit = 100

db_prefix = 'wg_'
summaryFolder = '../eigen/summary'
if sys.argv[1] == 'nds':
	db_prefix = ''
	summaryFolder = '../eigen/summary2'

if len(sys.argv) > 2 and sys.argv[2] == 'reset':
	shall = raw_input("Sure you want to reset DB? (y/N) ").lower() == 'y'
	if shall:
		cur.execute("TRUNCATE TABLE `"+db_prefix+"cluster`")
		cur.execute("UPDATE "+db_prefix+"page SET cluster1=0, cluster2=0, cluster3=0, cluster4=0, cluster5=0")

for i in range(0,20):
	count = cur.execute('SELECT id, level FROM '+db_prefix+'cluster WHERE complete=0 ORDER BY id LIMIT 1')
	level = -1; cluster = -1;
	if count > 0:
		rs = cur.fetchall()
		cluster = int(rs[0][0]); level = int(rs[0][1]);
	else:
		count = cur.execute('SELECT id, level FROM '+db_prefix+'cluster')
		if count < 1:
			level = 0; cluster = 0; # we have to build the first one.

	if level >= 0: # there's still something to run

		print "Level: ", level, ". Cluster:", cluster
		
		createGraph(limit, level, cluster, db_prefix)
		print "Built graph for community detection"

		buildCommunity()
		print "Ran community detection"

		useNLP(summaryFolder)
		print "Reassign nodes with NLP + name communities"

		QA()
		print "Ran Q&A check on comunities"

		extrapolate(summaryFolder)
		print "Extrapolated other nodes"

		saveResults(level, cluster, db_prefix)
		print "Saved results to database."
		print "All done.\n----------------------------"