from dbco import *
from igraph import *

def build_graph(arts):
	G = Graph()
	G.add_vertices(len(arts))
	findIndex = {};
	for i, a in zip(range(0,len(arts)), arts):
		findIndex[int(a)] = i;

	cur.execute("SELECT `from`, `to` FROM link WHERE `to` IN ("+','.join(arts)+") AND `from` IN ("+','.join(arts)+")")
	edges = [(findIndex[int(r[0])], findIndex[int(r[1])]) for r in cur.fetchall()];
	G.add_edges(edges)
	return G