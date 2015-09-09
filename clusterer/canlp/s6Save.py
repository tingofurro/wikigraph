from dbco import *

def saveResults(level, cluster, db_prefix):
	f = open('data/reclusters.txt','r')
	txt = f.read(); f.close();
	clusterMapping = {}
	for line in txt.split('\n'):
		toks = line.split('[]')
		if(len(toks) > 2):
			if toks[1] == 'nan':
				score = 0
			else:
				score = float(toks[1])
			clus = int(toks[0]); name = toks[2]; good = toks[3];
			isComplete = '1';
			if score > 0.5 and clus != 0 and level<4:
				isComplete = '0'
			query = "INSERT INTO `"+db_prefix+"cluster` (`id`, `parent`, `name`, `level`, `score`, `complete`, `good`) VALUES (NULL, '"+str(cluster)+"', '"+name+"', '"+str(level+1)+"', '"+str(score)+"', '"+isComplete+"', '"+good+"');"
			cur.execute(query)
			cur.execute("SELECT id FROM "+db_prefix+"cluster ORDER BY id DESC LIMIT 1")
			clusterMapping[clus] = cur.fetchall()[0][0]
	# Finished creating clusters, time to update pages
	query = "UPDATE "+db_prefix+"page SET cluster"+str(level+1)+" = CASE id "
	pageIds = []
	for fName in ['recommunity', 'extrapolate']:
		f = open('data/'+fName+'.txt','r')
		txt = f.read(); f.close();
		for line in txt.split('\n'):
			toks = line.split(' ')
			if len(toks) > 1:
				query += 'WHEN '+toks[0]+' THEN '+str(clusterMapping[int(toks[1])])+' '
				pageIds.append(toks[0])
	query += 'END WHERE id IN ('+','.join(pageIds)+')'
	cur.execute(query)
	cur.execute("UPDATE "+db_prefix+"cluster SET complete=1 WHERE level="+str(level)+" AND id="+str(cluster))