from dbco import *
from igraph import *
from igraph import datatypes
import numpy as np
from numpy import linalg as LA



g = Graph()
g.add_vertices(12)

g.add_edges([(1,7), (1,4), (4,7)])
g.add_edges([(2,5), (2,8), (2,11), (5,8), (5,11), (8,11)])
g.add_edges([(3,6), (3,9), (3,10), (3,0), (6,9), (6,10), (6,0), (9,10), (9,0), (0,10)])

g.add_edges([(4,2), (6,11), (7,9)])

# g.add_edges([(7,11), (2,10)]) # add dust

A = np.array(g.get_adjacency().data) # stuff you have to do
D = g.outdegree() * np.identity(len(A[0]))

# L = D-A
L = A


e_vals, e_vect = LA.eig(L)

e_vect[e_vect<0] = 0
e_vect[e_vect>0] = 1

bestI = e_vals.argsort()[-len(e_vals):][::-1]
for i in bestI:
	print e_vals[i], "=>", e_vect[i]