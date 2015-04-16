from igraph import *

g = Load('graph.json', 'ncol')
clus = g.community_infomap()