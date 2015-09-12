from igraph import *
from dbco import *
import numpy as np

cur.execute("SELECT id, level FROM cluster WHERE id<=200 ORDER BY id")
for row in cur.fetchall():
	clusterId = row[0]
	level = row[1]

	cur.execute("SELECT id, name FROM page WHERE cluster"+str(level)+"="+str(clusterId)); f = cur.fetchall();
	pages = [str(row[0]) for row in f]; pageNames = [str(row[1]) for row in f];
	cur.execute("SELECT id FROM page WHERE cluster"+str(level)+"="+str(clusterId))

	cur.execute("SELECT `from`, `to` FROM header_link WHERE `from` IN ("+','.join(pages)+") AND `to` IN ("+','.join(pages)+")")
	link = [(str(row[0]), str(row[1])) for row in cur.fetchall()]

	g = Graph()
	g.add_vertices(pages)
	g.add_edges(link)
	PR = np.array(g.pagerank())
	betweenness = np.array(g.betweenness())
	inDegree = np.array(g.degree(type="in"))
	centre = (PR/np.mean(PR)+betweenness/np.mean(betweenness)+inDegree/np.mean(inDegree))
	bestCentre = centre.argsort()[-2:][::-1]
	cur.execute("UPDATE cluster SET name=\""+pageNames[bestCentre[0]]+"\" WHERE id="+str(clusterId))
	print clusterId, "-> ", pageNames[bestCentre[0]], " ", centre[bestCentre[0]], " vs. ", centre[bestCentre[1]]