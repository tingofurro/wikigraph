from igraph import *
from myLouvain import *
from myLouvain2 import *
from collections import Counter

def buildCommunity(G, G2):
	K= 6; minSize = int(G.vcount()/(K+1))
	m = constrainedLouvain2(G, G2, minSize)

	mTotal = m
	# mTotal = [0]*G.vcount()
	# for node, member in zip(Ggiant.vs, m):
	# 	mTotal[node.index] = (member+1)

	print "Ran community detection"
	return mTotal