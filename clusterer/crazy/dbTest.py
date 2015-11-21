from dbco import *
from igraph import *
import numpy as np
from numpy import linalg as LA

cur.execute("SELECT id FROM wg_cluster WHERE level=1 ORDER BY id")

clusterList = []
for row in cur.fetchall():
	clusterList.append(str(int(row[0])))

pageList = []
pageName = []
for c in clusterList:
	cur.execute("SELECT id, name FROM wg_page WHERE cluster1="+c+" ORDER BY PR DESC LIMIT 6");
	for row in cur.fetchall():
		pageName.append(row[1])
		pageList.append(str(row[0]))


pageListString = ','.join(pageList)

g = Graph()
g.add_vertices(len(pageList))

cur.execute("SELECT `from`, `to` FROM wg_link WHERE `from` IN ("+pageListString+") AND `to` IN ("+pageListString+")")

for row in cur.fetchall():
	toNode = str(int(row[1]))
	fromNode = str(int(row[0]))
	g.add_edges([(pageList.index(fromNode), pageList.index(toNode))])


adja = g.laplacian()
adja = np.array(adja.data) # stuff you have to do

e_vals, e_vect = LA.eig(adja)
e_vect = 100*e_vect
e_vect = e_vect.astype(int)

# e_vect = e_vect.transpose(1,0)

i = 0
for eval in e_vals:
	if eval < 0:
		break
	v = e_vect[i]
	maxVal = max(abs(v))
	if min(v) < 0 and -min(v) == maxVal:
		v = -v
	o = 0
	myCluster = []
	for u in v:
		if u > (maxVal/2):
			myCluster.append(pageList[o])
			print pageName[o]
		o += 1
	i += 1
#	print myCluster
	print "-----------------------------------------"
