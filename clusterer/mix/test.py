from dbco import *
import sys

# 0.33111

def getLinkScore(prefix):
	maxLvl = 3;
	counts = {}; clus = {};

	cur.execute("SELECT id, cluster1, cluster2, cluster3, cluster4, cluster5 FROM "+prefix+"page ORDER BY id"); res = cur.fetchall();
	for lvl in range(1,maxLvl+1):
		clus[lvl] = [r[lvl] for r in res];
		counts[lvl] = 0;

	cur.execute("SELECT `from`, `to` FROM "+prefix+"link ORDER BY id"); res = cur.fetchall();
	M = len(res);

	for ed in res:
		for lvl in range(1,maxLvl+1):
			if(clus[lvl][ed[0]-1] == clus[lvl][ed[1]-1]):
				counts[lvl] += 1

	return float(counts[1]+counts[2]+counts[3])/(3*M)

if __name__ == '__main__' and len(sys.argv) > 1:
	prefix = sys.argv[1]+'_'
	print getLinkScore(prefix)