from igraph import *
import louvain
from myLouvain import *
from collections import Counter

def buildCommunity(G):
	K= 6; minSize = int(G.vcount()/(K+1))
	m = constrainedLouvain(G, minSize)
	print "Ran community detection"
	return m