from igraph import *
from modularity import *
import copy
import louvain
roun = 0
comm = {}; deg = {}; self = {}; Sin = {}; Stot = {}; nei = {};

def Louvain(G, p):
	global roun, deg, self, Sin, Stot, nei, comm
	comm = {}; deg = {}; self = {}; Sin = {}; Stot = {}; nei = {};
	for e in G.get_edgelist():
		deg[e[0]] = deg.get(e[0],0) + 1; deg[e[1]] = deg.get(e[1],0) + 1;
		Stot[p[e[0]]] = Stot.get(e[0],0) + 1; Stot[p[e[1]]] =  Stot.get(e[1],0)+ 1;
		if e[0] in nei:
			nei[e[0]].append(e[1])
		else:
			nei[e[0]] = [e[1]]
		if e[1] in nei:
			nei[e[1]].append(e[0])
		else:
			nei[e[1]] = [e[0]]
		if e[0] == e[1]:
			Sin[p[e[0]]] = Sin.get(p[e[0]], 0) + 2; self[e[0]] = self.get(e[0], 0) + 2;
		if p[e[0]] not in comm:
			comm[p[e[0]]] = [e[0]]
		if p[e[1]] not in comm:
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
			removeMove(p, i); p[i] = -1
			for c in comms:
				myDM = deltaMod1Side(i, c)
				if myDM > dm or best_c == -1:
					dm = myDM; best_c = c
			if not best_c == old_c:
				improve = True; changes += 1;
			p[i] = best_c; insertMove(p, i);

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
	global roun, deg, self, Sin, Stot, nei, comm
	myP = p[i]
	comm[myP].remove(i)
	Sin[myP] = Sin.get(myP,0) - (2*intersect_length(nei[i], comm[myP]) + self.get(i,0))
	Stot[myP] -= deg[i]

def insertMove(p, i):
	global roun, deg, self, Sin, Stot, nei, comm
	myP = p[i]
	Sin[myP] = Sin.get(myP,0) + (2*intersect_length(nei[i], comm[myP]) + self.get(i,0))
	Stot[myP] += deg[i]
	comm[myP].append(i)

def deltaMod1Side(node, new_m):
	global roun, deg, self, Sin, Stot, nei, comm
	S_tot = Stot[new_m]; S_in = Sin.get(new_m,0);
	s = (2.0*G.ecount()); k_i = deg[node];
	k_i_in = 2*intersect_length(nei[node], comm[new_m])
	return ((S_in+k_i_in)/s - ((S_tot+k_i)/s)**2)-(S_in/s - (S_tot/s)**2 - (k_i/s)**2)

def intersect_length(neighbors, friends):
	return len([c for c in neighbors if c in friends])

G = build_graph(20000)
p1 = louvain.find_partition(G, method='Modularity').membership
print "Best Louvain modularity: ", G.modularity(p1)
p2 = Louvain(G, np.array(range(0,G.vcount())))
print len(list(set(p1))), "vs. ", len(list(set(p2)))
print "My Louvain modularity:   ", G.modularity(p2)