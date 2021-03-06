from igraph import *
import louvain
from collections import Counter

def Louvain(G):
	global comm, deg, self, Sin, Stot, nei, num_edges
	p = buildGraphVars(G)
	improve = True; changes = 0;
	while improve:
		improve = False
		for i in range(0,G.vcount()):
			old_c = p[i]; best_c = bestComm(p, i, False)
			p = insertMove(p, i, best_c);
			if best_c != old_c:
				improve = True; changes += 1
	p, pSet = removeGaps(p)
	if changes == 0:
		return p
	else:
		newP = Louvain(collapseGraph(G, p))
		return relabel(p, pSet, newP)

def bestComm(p, i, force):
	comms = set([p[ne] for ne in nei[i].keys()]);
	comms.add(p[i])
	if force:
		comms.remove(p[i])
	p = removeMove(p, i);
	dms = {c: deltaMod1Side(i,c) for c in comms}
	return max(dms, key=dms.get);
def removeGaps(p):
	pSet = list(set(p))
	p = [pSet.index(oldP) for oldP in p]; pSet = list(set(p))
	return p, pSet
def buildGraphVars(G):
	global comm, deg, self, Sin, Stot, nei, num_edges
	comm = {}; deg = {}; self = {}; Sin = {}; Stot = {}; nei = {};
	p = range(0,G.vcount()); num_edges = G.ecount()
	for v in range(0,G.vcount()):
		nei[v] = Counter([ne.index for ne in G.vs[v].neighbors()])
		comm[p[v]] = set([v]); deg[v] = sum(nei[v].values());
		self[v] = 2*len(G.es.select(_within=[v]));
		Sin[p[v]] = self[v]; Stot[p[v]] = deg[v];
	return p
def collapseGraph(G, p):
	G2 = Graph()
	n_edges = [(p[e[0]], p[e[1]]) for e in G.get_edgelist()]
	G2.add_vertices(max(p)+1); G2.add_edges(n_edges);
	return G2
def removeMove(p, i):
	myP = p[i];	comm[myP].remove(i)
	Sin[myP] = Sin[myP] - (2*intersect_length(nei[i], comm[myP]) + self[i])
	Stot[myP] -= deg[i]; p[i] = -1
	return p
def insertMove(p, i, myP):
	Sin[myP] = Sin[myP] + (2*intersect_length(nei[i], comm[myP]) + self[i])
	p[i] = myP; Stot[myP] += deg[i]; comm[myP].add(i)
	return p
def deltaMod1Side(node, new_m):
	S_tot = Stot[new_m]; S_in = Sin[new_m];
	s = (2.0*num_edges); k_i = deg[node];
	k_i_in = 2*intersect_length(nei[node], comm[new_m])
	return ((S_in+k_i_in)/s - ((S_tot+k_i)/s)**2)-(S_in/s - (S_tot/s)**2 - (k_i/s)**2)
def relabel(p, pSet, newP):
	relabeling = {o: n for o, n in zip(pSet, newP)}
	return [relabeling[pOld] for pOld in p]
def intersect_length(neighbors, friends):
	goodNodes = set(neighbors.keys()) & friends
	return sum([neighbors[n] for n in goodNodes])
def constrainedLouvain(G, minSize):
	# Cannot have a cluster of size less than minSize
	p = Louvain(G)
	count = Counter(p)
	c = min(count, key=count.get); cSize = count[c]
	while cSize < minSize:
		G2 = collapseGraph(G, p)
		buildGraphVars(G2)
		pOld = range(0,max(p)+1); pNew = range(0,max(p)+1);
		pNew[c] = bestComm(pOld, c, True); pOld[c] = c;
		if pNew[c] == -1:
			pNew[c] = max(count, key=count.get);
		p, pSet = removeGaps(relabel(p, pOld, pNew))
		count = Counter(p)
		c = min(count, key=count.get); cSize = count[c]
	return p