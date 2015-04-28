from itertools import izip
from igraph import *
import csv

root = sys.argv[1]

g = Graph.Load(root+'/igraph/data/graph.json', 'ncol')

pieces = g.clusters()

g = pieces.giant() # just keep the largest piece, there can be annoying islands

comm = g.community_spinglass()
membership = comm.membership

members = {}
for mem in membership:
	members[mem] = members.get(mem, 0) + 1

for i, member in zip(range(0,g.vcount()), membership):
	membership[i] += 1
	if members.get(member, 0) < (0.05*g.vcount()): # remove clusters that are too small, call them 0
		membership[i] = 0

members = sorted(list(set(membership)))

for i, member in zip(range(0,g.vcount()), membership): # make sure it is compact, with 0 being soup
	membership[i] = members.index(membership[i])

f = open(root+'/igraph/data/community.txt','w')
for name, member in izip(g.vs["name"], membership):
	f.write(name+' '+str(member)+'\n')
f.close()