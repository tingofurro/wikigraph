from itertools import izip
from igraph import *
import csv

g = Graph.Load('graph.json', 'ncol')
# algos = ['community_infomap', 'community_leading_eigenvector', 'community_label_propagation', 'community_spinglass', 'community_walktrap']
# algoNames = ['infomap', 'leading_eigenvector_naive', 'label_propagation', 'spinglass', 'walktrap']
algos = ['community_infomap', 'community_spinglass']
algoNames = ['infomap', 'spinglass']

#fastgreedy, community_multilevel doesn't work

# layout_f = g.layout_fruchterman_reingold()

print "Finished preparing layout\n"

for algoName, func in zip(algoNames, algos):
	comm = eval('g.'+func+'()')

	plot(comm, algoName+".png", vertex_size=4, edge_arrow_size=0.1, edge_width=0.4, edge_curved=True)

	# membership = 
	print "Finished algo ", algoName, "\n"