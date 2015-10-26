from dbco import *

clus = 10
prefix = 'ee_'

cur.execute("SELECT id, level, name FROM "+prefix+"cluster WHERE id="+str(clus)); res = cur.fetchall()[0];
print res
oldName = res[2]; level = res[1];

cur.execute("SELECT id, name FROM "+prefix+"page WHERE cluster"+str(level)+"="+str(clus)); res = cur.fetchall();
pageNames = [r[1] for r in res];
pageSet = set(pageNames)
pageIds = [r[0] for r in res];
pageScores = {}

for name in pageNames:
	linkList = open('../../crawler/pageLinks/'+name+'.txt').read().split('\n')
	for link in linkList:
		if link in pageSet:
			pageScores[link] = pageScores.get(link,0)+1
			break

print pageScores