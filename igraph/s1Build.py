from dbco import *

def createGraph(limit, level, cluster):
	nodes = []; where = '';
	if(level > 0):
		where = ' WHERE cluster'+str(level)+'='+str(cluster)
	cur.execute("SELECT id FROM wg_page"+where+" ORDER BY PR DESC LIMIT "+str(limit))
	for row in cur.fetchall():
		nodes.append(str(row[0]))

	f = open(root+'/igraph/data/fullNodeList.txt','w')
	cur.execute("SELECT id FROM wg_page"+where)
	for row in cur.fetchall():
		f.write(str(row[0])+'\n')		
	f.close()

	f = open(root+'/igraph/data/graph.json','w')
	cur.execute("SELECT `from`, `to` FROM wg_link WHERE (`to` IN ("+','.join(nodes)+") AND `from` IN ("+','.join(nodes)+")) ORDER BY id")
	for row in cur.fetchall():
		f.write(str(row[0])+' '+str(row[1])+'\n')
	f.close()