from dbco import *
from igraph import *
import numpy as np
from numpy import linalg as LA

cur.execute("SELECT id FROM wg_page ORDER BY PR DESC LIMIT 1000")

pageList = []

for row in cur.fetchall():
	pageList.append(str(row[0]))


g = Graph(directed=True)
g.add_vertices(len(pageList))


pageListString = ','.join(pageList)
cur.execute("SELECT `from`, `to` FROM wg_link WHERE `from` IN ("+pageListString+") AND `to` IN ("+pageListString+")")
for row in cur.fetchall():
	toNode = str(int(row[1]))
	fromNode = str(int(row[0]))
	g.add_edges([(pageList.index(toNode), pageList.index(fromNode))])
adja = g.get_adjacency()

adja = np.array(adja.data)

e_vals, e_vect = LA.eig(adja)

for eval in e_vals:
	if eval > 0:
		print eval