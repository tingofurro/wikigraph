from itertools import izip
from igraph import *
import csv

g = Graph.Load('data/graph.json', 'ncol')
# algos = ['community_infomap', 'community_leading_eigenvector', 'community_label_propagation', 'community_spinglass', 'community_walktrap']
# algoNames = ['infomap', 'leading_eigenvector_naive', 'label_propagation', 'spinglass', 'walktrap']
algos = ['community_spinglass', 'community_leading_eigenvector']
algoNames = ['spinglass', 'eigenvector']

layout_f = g.layout_fruchterman_reingold()

print "Finished preparing layout"

for algoName, func in zip(algoNames, algos):
	comm = eval('g.'+func+'()')

	plot(comm, "data/"+algoName+".png", layout=layout_f, vertex_size=4, edge_arrow_size=0.1, edge_width=0.2)

	# membership = comm.membership
	f = open('data/'+algoName+'.txt','w')
	for name, member in izip(g.vs["name"], membership):
		f.write(name+' '+str(member)+'\n') # python will convert \n to os.linesep
	f.close()

	print "Finished algo ", algoName, ""