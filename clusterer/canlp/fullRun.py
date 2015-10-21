#!/usr/bin/python
import shutil

from dbco import *
from s1Build import createGraph
from s2Community import buildCommunity
from s3Nlp import useNLP
from s4QA import QA
from s5Extrapolate import extrapolate
from s6Save import saveResults
from s7Label import labelCluster

def tryCatchCreate(prefix, name):
	try:
		cur.execute("ALTER TABLE `"+prefix+"page` ADD `"+name+"` int(11) NOT NULL ")
	except:
		pass

limit = 4000

db_prefix = 'ma_'
if len(sys.argv) > 1:
	db_prefix = sys.argv[1]+'_'
summaryFolder = '../../crawler/summary/'+db_prefix

cur.execute("CREATE TABLE IF NOT EXISTS `"+db_prefix+"cluster` (`id` int(11) NOT NULL AUTO_INCREMENT,`parent` int(11) NOT NULL,`name` text NOT NULL,`level` int(11) NOT NULL,`score` float(10,4) NOT NULL,`size` int(11) NOT NULL,`complete` int(11) NOT NULL,PRIMARY KEY (`id`)) DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;");
cur.execute("TRUNCATE TABLE `"+db_prefix+"cluster`")
tryCatchCreate(db_prefix, 'cluster1'); tryCatchCreate(db_prefix, 'cluster2')
tryCatchCreate(db_prefix, 'cluster3'); tryCatchCreate(db_prefix, 'cluster4')
tryCatchCreate(db_prefix, 'cluster5');
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
		
		needForExtrapolate = createGraph(limit, level, cluster, db_prefix)
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

		labelCluster(db_prefix)

		print "All done.\n----------------------------"
		cur.execute('SELECT COUNT(*) FROM '+db_prefix+'cluster WHERE complete=0 AND level<=2')
		if cur.fetchall()[0][0] == 0:
			break;
