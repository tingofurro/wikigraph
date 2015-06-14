from itertools import izip
from igraph import *
import csv

root = sys.argv[1]

gOrig = Graph.Load(root+'/igraph/data/graph.json', 'ncol')

print "Original size ", gOrig.vcount()

pieces = gOrig.clusters()

g = pieces.giant() # just keep the largest piece, there can be annoying islands

comm = g.community_spinglass()
membership = comm.membership

members = {}
for mem in membership:
	members[mem] = members.get(mem, 0) + 1

f = open(root+'/igraph/data/community_old.txt','w')
for name, member in izip(g.vs["name"], membership):
	f.write(name+' '+str(member)+'\n')
f.close()

for i, member in zip(range(0,g.vcount()), membership):
	membership[i] += 1
	if members.get(member, 0) < min(5, 0.03*g.vcount()): # remove clusters that are too small, call them 0
		membership[i] = 0

memberSet = set(membership)
memberSet.add(0) # just in case
membersRelabel = sorted(list(memberSet))

for i, member in zip(range(0,g.vcount()), membership): # make sure it is compact, with 0 being soup
	membership[i] = membersRelabel.index(membership[i])

f = open(root+'/igraph/data/community.txt','w')
for name, member in izip(g.vs["name"], membership):
	f.write(name+' '+str(member)+'\n')

giantNames = g.vs["name"]

for vertex in gOrig.vs["name"]:
	if vertex not in giantNames:
		f.write(vertex+' '+str(0)+'\n')
f.close()