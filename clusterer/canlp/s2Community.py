from igraph import *

def buildCommunity():
	gOrig = Graph.Load('data/graph.json', 'ncol')

	pieces = gOrig.clusters()
	g = pieces.giant() # just keep the largest piece, there can be annoying islands

	# comm = g.community_spinglass() #promising but slow
	# comm = g.community_infomap() # should be promising... doesn't work :'(
	g.to_undirected(); comm = g.community_leading_eigenvector() #(fairly promising)
	# g.to_undirected(); comm = g.community_fastgreedy().as_clustering(); #(not good at all)
	# g.to_undirected(); comm = g.community_label_propagation() #(not very good)
	# g.to_undirected(); comm = g.community_multilevel() #decent
	# comm = g.community_optimal_modularity() "shell killed the process, was taking forever"
	# comm = g.community_edge_betweenness() # I feel asleep waiting
	# comm = g.community_walktrap().as_clustering() # Not bad

	membership = comm.membership;

	# print "Spinglass modularity: ", g.modularity(membership)

	members = {}
	for mem in membership:
		members[mem] = members.get(mem, 0) + 1

	for i, member in zip(range(0,g.vcount()), membership):
		membership[i] += 1
		if members.get(member, 0) < min(5, 0.03*g.vcount()): # remove clusters that are too small, call them 0
			membership[i] = 0

	memberSet = set(membership)
	memberSet.add(0) # just in case
	membersRelabel = sorted(list(memberSet))

	for i, member in zip(range(0,g.vcount()), membership): # make sure it is compact, with 0 being soup
		membership[i] = membersRelabel.index(membership[i])

	f = open('data/community.txt','w')
	for name, member in zip(g.vs["name"], membership):
		f.write(name+' '+str(member)+'\n')

	giantNames = g.vs["name"]

	for vertex in gOrig.vs["name"]:
		if vertex not in giantNames:
			f.write(vertex+' '+str(0)+'\n')
	f.close()