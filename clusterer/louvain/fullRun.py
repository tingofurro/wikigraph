#!/usr/bin/python
import shutil

from dbco import *
from s1Build import *
from s2Community import buildCommunity
from s3Nlp import useNLP
from s4Save import saveResults
from s5Label import labelCluster

db_prefix = ''
summaryFolder = '../../crawler/summary'

if len(sys.argv) > 1 and sys.argv[1] == 'reset':
	shall = raw_input("Sure you want to reset DB? (y/N) ").lower() == 'y'
	if shall:
		cur.execute("TRUNCATE TABLE `"+db_prefix+"cluster`")
		cur.execute("UPDATE "+db_prefix+"page SET cluster1=0, cluster2=0, cluster3=0, cluster4=0, cluster5=0")

while True:
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
		
		G, nodes = createGraph(level, cluster, db_prefix)
		# G2 = createGraphNLP(nodes, G.ecount())

		membership = buildCommunity(G, G2)

		membership = useNLP(nodes, membership, summaryFolder)

		saveResults(level, cluster, nodes, membership, db_prefix)

		labelCluster()

		print "\n----------------------------"
		cur.execute('SELECT COUNT(*) FROM '+db_prefix+'cluster WHERE complete=0 AND level<=2')
		if cur.fetchall()[0][0] == 0:
			break;
