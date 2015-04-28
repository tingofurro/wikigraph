from itertools import izip
from igraph import *
import csv

root = sys.argv[1]

g = Graph.Load(root+'/igraph/data/graph.json', 'ncol')

layout_f = g.layout_fruchterman_reingold()
print "Finished preparing layout"

pieces = g.clusters()

# subgraphs = pieces.subgraphs()
# for subgraph in subgraphs:
# 	print "This piece has ", subgraph.vcount() ," nodes"

print "Number of subgraphs: ", len(pieces.subgraphs())
g = pieces.giant() # just keep the largest piece, there can be annoying islands
print "Giant subgraph has", g.vcount(), " nodes"

comm = g.community_spinglass()

plot(comm, root+"/igraph/data/spinglass.png", layout=layout_f, vertex_size=4, edge_arrow_size=0.1, edge_width=0.2)

membership = comm.membership
f = open(root+'/igraph/data/spinglass.txt','w')
for name, member in izip(g.vs["name"], membership):
	f.write(name+' '+str(member)+'\n')
f.close()

print "Finished algo spinglass"