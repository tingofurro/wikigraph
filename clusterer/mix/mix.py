from sklearn.feature_extraction.text import CountVectorizer, TfidfTransformer
from sklearn.metrics.pairwise import cosine_similarity, euclidean_distances
from dbco import *
from igraph import *
import numpy as np

limit = 1000

cur.execute("SELECT id, name FROM page ORDER BY PR DESC LIMIT "+str(limit))
arts = []; artsName = [];
for art in cur.fetchall():
	arts.append(str(art[0]))
	artsName.append(art[1])

artsList = ','.join(arts)
edges = []
cur.execute("SELECT `from`, `to` FROM link WHERE `to` IN ("+artsList+") AND `from` IN ("+artsList+")")
f = open('graph.json','w')
for row in cur.fetchall():
	f.write(str(row[0])+' '+str(row[1])+'\n')
f.close()

G1 = Graph.Load('graph.json', 'ncol')

arpack_options.maxiter=300000; comm = G1.community_leading_eigenvector().membership #(fairly promising)
print G1.modularity(comm)
# print comm.membership

A = np.array(G1.get_adjacency().data)

texts = [];
for a in arts:
	f = open('../../crawler/summary/'+a+'.txt', "r"); texts.append(f.read()); f.close();

count_vect = CountVectorizer(stop_words='english', ngram_range = (1,2), binary=True)
totalCount = count_vect.fit_transform(texts)

tfidf_trans = TfidfTransformer() #initialize our tfidf transformer
totalCount = tfidf_trans.fit_transform(totalCount)

totalCount = totalCount.asfptype()

B = cosine_similarity(totalCount)
B[B==1] = 0

decimals = 2
C = ((pow(10, decimals)*(A+2*B)).astype(int))
C[C<0] = 0
np.savetxt('nadja.json', C, '%d')

G2 = Graph.Load('nadja.json', "adjacency")

arpack_options.maxiter=300000; comm2 = G2.community_leading_eigenvector().membership
print G1.modularity(comm2)