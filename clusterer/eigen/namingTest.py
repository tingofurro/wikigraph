from dbco import *
import heapq

cluster = int(sys.argv[1])
for cluster in range(2,20):
	cur.execute("SELECT level, name FROM cluster WHERE id="+str(cluster))
	row = cur.fetchall()[0]
	level = row[0]
	print row[1]

	cur.execute("SELECT keywords, PR FROM page WHERE cluster"+str(level)+"="+str(cluster))
	keywordCounts = {}
	for row in cur.fetchall():
		keywords = row[0].split(",")
		for k in keywords:
			if k not in keywordCounts:
				keywordCounts[k] = 0
			keywordCounts[k] += row[1]

	fiveBestKeywords = heapq.nlargest(5, keywordCounts, key=keywordCounts.get)
	print ','.join(fiveBestKeywords)
	print "-----------------------"