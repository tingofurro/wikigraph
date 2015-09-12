import numpy as np
from igraph import *
from dbco import *
import louvain

arpack_options.maxiter=300000;
def leadingEigenvectors(G, similarities):
	return G.community_leading_eigenvector().membership
def fastGreedy(G, similarities):
	G.to_undirected("collapse")
	G.simplify()
	return G.community_fastgreedy().as_clustering().membership
def infomap(G, similarities):
	return G.community_infomap().membership
def labelPropagation(G, similarities):
	return G.community_label_propagation().membership
def multilevel(G, similarities):
	return G.community_multilevel().membership
def edge_betweenness(G, similarities):
	return G.community_edge_betweenness().as_clustering().membership
def spinglass(G, similarities):
	return G.community_spinglass().membership
def walktrap(G, similarities):
	return G.community_walktrap().as_clustering().membership
def louvainMethod(G, similarities):
	G2 = G
	G2.to_undirected()
	return louvain.find_partition(G2, method='Modularity').membership
def backLouvainMethod(G, G2):
	G2.to_undirected()
	return louvain.find_partition(G2, method='Modularity').membership

# def leadingEigenvectorsNaive(G, similarities):
# 	return G.community_leading_eigenvector_naive().membership
# def optimal_modularity(G, similarities):
# 	return G.community_optimal_modularity().membership
