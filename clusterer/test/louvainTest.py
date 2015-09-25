from igraph import *
import numpy as np
from dbco import *
import louvain

cur.execute("SELECT id, cluster1 FROM page ORDER BY id")
dbFetch = cur.fetchall()
artId = [r[0] for r in dbFetch]; old_membership = [r[1] for r in dbFetch];

cur.execute("SELECT `from`, `to` FROM link ORDER BY id")
edges = [(r[0]-1, r[1]-1) for r in cur.fetchall()];

G = Graph()
G.add_vertices(len(artId));
G.add_edges(edges);

print G.vcount()
print G.ecount()

membership = louvain.find_partition(G, method='Modularity').membership

print "Modularity: ", G.modularity(membership)
print "Modularity: ", G.modularity(old_membership)

