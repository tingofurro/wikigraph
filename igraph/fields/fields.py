from itertools import izip
from igraph import *
import csv
import os


all_files = sorted(os.listdir('fields'))

for fileName in all_files:
	tokens = fileName.split(".")
	g = Graph.Load('fields/'+fileName, 'ncol')
	layout_f = g.layout_fruchterman_reingold()

	print "Finished preparing layout for ", tokens[0]

	comm = g.community_spinglass()

	plot(comm, "fields/"+tokens[0]+".png", vertex_size=4, layout = layout_f, edge_arrow_size=0.1, edge_width=0.4, edge_curved=True)
	
	membership = comm.membership
	f = open('fields/'+tokens[0]+'.txt','w')
	for name, member in izip(g.vs["name"], membership):
		f.write(name+' '+str(member)+'\n') # python will convert \n to os.linesep
	f.close()

	print "Finished field ", tokens[0]