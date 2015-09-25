from igraph import *
from dbco import *
import numpy as np
import louvain

def build_graph(limit):
	cur.execute("SELECT id FROM page ORDER BY betweenness DESC LIMIT "+str(limit))
	arts = [str(r[0]) for r in cur.fetchall()]
	G = Graph()
	G.add_vertices(len(arts))
	findIndex = {}
	for i, a in zip(range(0,len(arts)), arts):
		findIndex[int(a)] = i;

	cur.execute("SELECT `from`, `to` FROM link WHERE `to` IN ("+','.join(arts)+") AND `from` IN ("+','.join(arts)+")")
	edges = [(findIndex[int(r[0])], findIndex[int(r[1])]) for r in cur.fetchall()];
	G.add_edges(edges)
	return G
def wikiGraph():
	G = Graph()
	G.add_vertices(10)
	edges = [(0,1),(0,2),(0,9),(1,2),(3,4),(3,5),(3,9),(4,5),(6,7),(6,8),(6,9),(7,8)]
	G.add_edges(edges)
	return G

def compute_modularity(G, membership):
	clusters = list(set(membership))
	m = G.ecount()
	E_ij = {}
	for e in G.es:
		i = membership[e.tuple[0]]; j = membership[e.tuple[1]];
		E_ij[(i, j)] = E_ij.get((i,j), 0) + 1
		E_ij[(j, i)] = E_ij.get((j,i), 0) + 1

	e_ij = {}
	for c1,c2 in E_ij:
		e_ij[(c1,c2)] = E_ij[(c1,c2)]/(2.0*m)

	a_i = {}
	for i in clusters:
		a_i[i] = 0
		for j in clusters:
			a_i[i] += e_ij.get((i,j), 0)
	Q = 0
	for c in clusters:
		Q += (e_ij.get((c,c), 0) - a_i[c]**2)
	return Q

def compute_modularity_Matrix(G, membership):
	clusters = list(set(membership))
	m = G.ecount()
	A = np.array(G.get_adjacency().data)
	k_v = np.matrix(G.degree())
	B = A-((k_v.transpose()*k_v)/(2.0*m))
	S = np.zeros([len(clusters), len(membership)])
	for i, c in zip(range(0,len(membership)), membership):
		S[c][i] = 1
	return np.trace(S*B*S.transpose())/(2.0*m)

# G = build_graph(100)
# # G = wikiGraph()
# membership = louvain.find_partition(G, method='Modularity').membership

# print "Igraph Modularity:    ", G.modularity(membership)

# print "My similarity:        ", compute_modularity(G, membership)

# print "My matrix similarity: ", compute_modularity_Matrix(G, membership)