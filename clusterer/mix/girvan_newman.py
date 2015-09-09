import networkx as nx
import math, csv, sys

def buildG(G, Gi):
    for u,v in Gi.get_edgelist():
        G.add_edge(int(u),int(v),weight=1.0)

callNb = 0
step = 0
def girvan_newman(G):
    global callNb
    global step
    callNb += 1
    print "Call number:", callNb
    if len(G.nodes()) == 1:
        return [G.nodes()]

    def find_best_edge(G0):
        """
        Networkx implementation of edge_betweenness
        returns a dictionary. Make this into a list,
        sort it and return the edge with highest betweenness.
        """
        eb = nx.edge_betweenness_centrality(G0)
        eb_il = eb.items()
        eb_il.sort(key=lambda x: x[1], reverse=True)
        return eb_il[0][0]

    components = list(nx.connected_component_subgraphs(G))
    while len(components) == 1:
        G.remove_edge(*find_best_edge(G))
        step += 1
        print 'Steps: ', step
        components = list(nx.connected_component_subgraphs(G))

    result = [c.nodes() for c in components]

    for c in components:
        result.extend(girvan_newman(c))

    return result

def mainGN(Gi, bli):
    G = nx.Graph()  #let's create the graph first
    buildG(G, Gi)

    print girvan_newman(G)