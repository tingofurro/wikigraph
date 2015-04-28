from itertools import izip
from igraph import *
import csv

root = sys.argv[1]

g = Graph.Load(root+'/igraph/data/graph.json', 'ncol')
pieces = g.clusters()
g = pieces.giant()

g.to_undirected()
#community_edge_betweenness
algos = ['community_spinglass', 'community_infomap', 'community_multilevel', 'community_fastgreedy', 'community_leading_eigenvector', 'community_label_propagation', 'community_walktrap']
algoNames = ['spinglass',       'infomap', 'multilevel',                     'fastgreedy',           'leading_eigenvector_naive',     'label_propagation',           '',           'walktrap']
#edge_betweenness
layout_f = g.layout_fruchterman_reingold()

print "Finished preparing layout\n ---------------------"

for algoName, func in zip(algoNames, algos):
	comm = eval('g.'+func+'()')
	if comm.__class__.__name__ == 'VertexDendrogram': # switch from dendrogram to clustering, to get a membership
		comm = comm.as_clustering()

	plot(comm, root+"/igraph/algos/"+algoName+".png", layout=layout_f, vertex_size=4, edge_arrow_size=0.1, edge_width=0.4, edge_curved=True)

	print "Finished algo ", algoName, ", modularity: ", g.modularity(comm)

	membership = comm.membership
	f = open(root+"/igraph/algos/"+algoName+".txt",'w')
	for name, member in izip(g.vs["name"], membership):
		f.write(name+' '+str(member)+'\n')
	f.close()