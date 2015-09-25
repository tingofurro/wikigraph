from igraph import *
from modularity import *
import copy
import louvain
roun = 0
comm = {}; deg = {}; self = {}; Sin = {}; Stot = {}; nei = {};

def Louvain(G, p):
	global roun, deg, self, Sin, Stot, nei
	comm = {}; deg = {}; self = {}; Sin = {}; Stot = {}; nei = {};
	for e in G.get_edgelist():
		deg[e[0]] += 1; deg[e[1]] += 1;
		Stot[p[e[0]]] += 1; Stot[p[e[1]]] += 1;
		if nei[e[0]]:
			nei[e[0]].append(e[1])
		else:
			nei[e[0]] = [e[1]]
		if nei[e[1]]:
			nei[e[1]].append(e[0])
		else:
			nei[e[1]] = [e[0]]
		if e[0] == e[1]:
			Sin[p[e[0]]] += 2; self[e[0]] += 2;
		if not comm[p[e[0]]]:
			comm[p[e[0]]] = [e[0]]
		if not comm[p[e[1]]]:
			comm[p[e[1]]] = [e[1]]
	roun += 1
	n = G.vcount()
	improve = True; changes = 0;
	while improve:
		improve = False
		for i in range(0,n):
			neighbors = [v.index for v in G.vs[i].neighbors()]
			comms = set([p[ne] for ne in neighbors]); comms.add(p[i])
			dm = -10; best_c = -1;
			old_c = p[i]
			p[i] = -1
			for c in comms:
				myDM = deltaMod1Side(G, p, i, c)
				if myDM > dm or best_c == -1:
					dm = myDM; best_c = c
			if not best_c == old_c:
				improve = True; changes += 1;
			p[i] = best_c

	pSet = list(set(p))
	p = [pSet.index(oldP) for oldP in p]
	pSet = range(0,len(pSet))
	if changes == 0:
		return p
	else:
		G2 = Graph()
		n_edges = []
		for e in G.get_edgelist():
			n_edges.append((p[e[0]], p[e[1]]))
			# if p[e[0]] == p[e[1]]: # add an exta one
			# 	n_edges.append((p[e[0]], p[e[1]]))

		G2.add_vertices(len(pSet))
		G2.add_edges(n_edges)
		newP = Louvain(G2, copy.deepcopy(pSet))
		relabeling = {o: n for o, n in zip(pSet, newP)}
		p = [relabeling[pOld] for pOld in p]
		return p

def removeMove(p, i):
	myP = p[i]
	Sin[myP] -= 

def insertMove(p, i):


def deltaMod1Side(G, membership, node, new_m):
	membership = np.array(membership)
	old_m = membership[node]
	if new_m == old_m:
		return 0
	newFriends = np.where(membership==new_m)[0]
	neighbors = [v.index for v in G.vs[node].neighbors()]

	if len(newFriends) == 1:
		S_tot = G.degree(newFriends)
	else:
		S_tot = sum(G.degree(newFriends));

	S_in = 2*len([e.tuple for e in  G.es.select(_within=newFriends)]);
	s = (2.0*G.ecount())
	k_i = G.degree(node)
	k_i_in = 2*intersect_length(newFriends, neighbors)
	return ((S_in+k_i_in)/s - ((S_tot+k_i)/s)**2)-(S_in/s - (S_tot/s)**2 - (k_i/s)**2)

def intersect_length(a, b):
	return len([c for c in b if c in a])

G = build_graph(1000)
p1 = louvain.find_partition(G, method='Modularity').membership
print "Best Louvain modularity: ", G.modularity(p1)
p2 = Louvain(G, np.array(range(0,G.vcount())))
print "My Louvain modularity:   ", G.modularity(p2)