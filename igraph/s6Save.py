from dbco import *

def saveResults(root, level, cluster):
	where = ''
	if level > 0:
		where = ' WHERE cluster'+str(level)+'='+str(cluster)
	f = open(root+'/igraph/data/reclusters.txt','r')
	txt = f.read(); f.close();
	clusterMapping = {}
	for line in txt.split('\n'):
		toks = line.split('[]')
		if(len(toks) > 2):
			clus = int(toks[0]); score = int(toks[1]); name = toks[2]; good = toks[3];
			isComplete = '1';
			if score > 1.5 and clus != 0 and level<4:
				isComplete = '0'
			query = "INSERT INTO `wg_cluster` (`id`, `parent`, `name`, `level`, `score`, `complete`, `good`) VALUES (NULL, '"+str(cluster)+"', '"+name+"', '"+str(level+1)+"', '"+str(score)+"', '"+isComplete+"', '"+good+"');"
			cur.execute(query)
			cur.execute("SELECT id FROM wg_cluster ORDER BY id DESC LIMIT 1")
			clusterMapping[clus] = cur.fetchall()[0][0]
	# Finished creating clusters, time to update pages
	query = "UPDATE"+" wg_page SET cluster"+str(level+1)+" = CASE id "
	pageIds = []
	for fName in ['recommunity', 'extrapolate']:
		f = open(root+'/igraph/data/'+fName+'.txt','r')
		txt = f.read(); f.close();
		for line in txt.split('\n'):
			toks = line.split(' ')
			if len(toks) > 1:
				query += 'WHEN '+toks[0]+' THEN '+str(clusterMapping[int(toks[1])])+' '
				pageIds.append(toks[0])
	query += 'END WHERE id IN ('+','.join(pageIds)+')'
	cur.execute(query)
	cur.execute("UPDATE wg_cluster SET complete=1 WHERE level="+str(level)+" AND id="+str(cluster))