from dbco import *
import sys

def findNewName(prefix, clus):
	cur.execute("SELECT id, level, name FROM "+prefix+"cluster WHERE id="+str(clus)); res = cur.fetchall()[0];
	oldName = res[2]; level = res[1];

	cur.execute("SELECT id, name FROM "+prefix+"page WHERE cluster"+str(level)+"="+str(clus)); res = cur.fetchall();
	pageNames = [r[1] for r in res];
	pageSet = set(pageNames)
	pageIds = [r[0] for r in res];
	print len(pageIds)
	pageScores = {}
	found = 0

	for name in pageNames:
		linkList = open('pageLinks/'+name+'.html').read().split('\n')
		# for link in linkList:
		if linkList[0] in pageSet:
			pageScores[linkList[0]] = pageScores.get(linkList[0],0)+1
			found += 1
			# break
	print found, " / ", len(pageNames)
	newA = sorted(pageScores, key=pageScores.get, reverse=True)[:5]
	print "[", clus,"]", oldName, " => ", newA[0]
# print pageScores

if __name__ == '__main__' and len(sys.argv) > 1:
	prefix = sys.argv[1]+'_'
	cur.execute("SELECT id FROM "+prefix+"cluster WHERE level=1 ORDER BY id LIMIT 20");
	for r in cur.fetchall():
		findNewName(prefix, r[0])