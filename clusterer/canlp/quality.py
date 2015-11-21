from dbco import *

prefix = 'ma_'
 
# Mathematics canlp: community_leading_eigenvector
# {0: 113541, 1: 85477, 2: 64729, 3: 88810}
# 1.36535368749

# Canlp: myLouvain
# {0: 109041, 1: 78388, 2: 53065, 3: 112063}
# 1.47694415371

cur.execute("SELECT id, cluster1, cluster2, cluster3 FROM "+prefix+"page")

cluster1 = {}; cluster2 = {}; cluster3 = {};

for p in cur.fetchall():
	cluster1[p[0]] = p[1]
	cluster2[p[0]] = p[2]
	cluster3[p[0]] = p[3]

cur.execute("SELECT `from`, `to` FROM "+prefix+"link"); edges = cur.fetchall();
totalScore = 0.0
edgeDepth = {}
for e in edges:
	thisScore = 0
	if cluster1[e[0]] == cluster1[e[1]]:
		thisScore += 1
	if cluster2[e[0]] == cluster2[e[1]] and cluster2[e[0]] != 0:
		thisScore += 1
	if cluster3[e[0]] == cluster3[e[1]] and cluster3[e[0]] != 0:
		thisScore += 1
	edgeDepth[thisScore] = edgeDepth.get(thisScore, 0)+1
	totalScore += thisScore
totalScore = totalScore/float(len(edges))

print edgeDepth
print totalScore