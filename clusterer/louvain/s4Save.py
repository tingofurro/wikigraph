from dbco import *
from collections import Counter

def saveResults(level, parent, nodes, membership, db_prefix):
	clusters = list(set(membership))
	pagesStr = [str(n) for n in nodes];

	counts = Counter(membership)
	# Create clusters in cluster table
	clusterMapping = {}
	for c in clusters:
		cur.execute("INSERT INTO `"+db_prefix+"cluster` (`id`, `parent`, `name`, `level`, `score`, `size`, `complete`) VALUES (NULL, '"+str(parent)+"', '', '"+str(level+1)+"', '0', '"+str(counts[c])+"', '0');")
		cur.execute("SELECT id FROM "+db_prefix+"cluster ORDER BY id DESC LIMIT 1")
		clusterMapping[c] = cur.fetchall()[0][0]

	# Update pages with new cluster assignment
	query = "UPDATE "+db_prefix+"page SET cluster"+str(level+1)+" = CASE id "
	caseStr = ['WHEN '+str(nodes[i])+' THEN '+str(clusterMapping[membership[i]]) for i in range(0,len(nodes))]
	query += ' '.join(caseStr)
	query += ' END WHERE id IN ('+','.join(pagesStr)+')'
	cur.execute(query)

	# Finished the parent cluster
	cur.execute("UPDATE "+db_prefix+"cluster SET complete=1 WHERE level="+str(level)+" AND id="+str(parent))
	print "Saved results to database."