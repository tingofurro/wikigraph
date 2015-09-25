from igraph import *
import louvain
from collections import Counter

def buildCommunity(G):
	n = G.vcount()
	minClusSize = max(5, int(0.03*n))

	m = louvain.find_partition(graph=G, method='Modularity').membership
	members = Counter(m)

	for i, member in zip(range(0,n), m):
		m[i] += 1
		if members[member] < minClusSize: # remove clusters that are too small, call them 0
			m[i] = 0

	memberSet = set(m); memberSet.add(0);
	membersRelabel = sorted(list(memberSet))

	for i, member in zip(range(0,n), m): # make sure it is compact, with 0 being soup
		m[i] = membersRelabel.index(m[i])

	print "Ran community detection"
	return m