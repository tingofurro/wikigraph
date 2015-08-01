from igraph import *
from numpy import linalg as LA
import numpy as np

g = Graph()
g.add_vertices(12)

g.add_edges([(1,7), (1,4), (4,7)])
g.add_edges([(2,5), (2,8), (2,11), (5,8), (5,11), (8,11)])
g.add_edges([(3,6), (3,9), (3,10), (3,0), (6,9), (6,10), (6,0), (9,10), (9,0)])

g.add_edges([(4,2), (6,11), (7,9)])

g.add_edges([(7,11), (2,10)]) # add dust

adja = g.get_adjacency()
adja = np.array(adja.data) # stuff you have to do

adja = g.laplacian()
print adja

e_vals, e_vect = LA.eig(adja)
e_vect = 100*e_vect
e_vect = e_vect.astype(int)

print e_vals
print "-----------------------"
print e_vect
