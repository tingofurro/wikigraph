from dbco import *
import heapq

cluster = int(sys.argv[1])

cur.execute("SELECT level FROM wg_cluster WHERE id="+str(cluster))
level = cur.fetchall()[0][0]

cur.execute("SELECT keywords FROM wg_page WHERE cluster"+str(level)+"="+str(cluster))
keywordCounts = {}
for row in cur.fetchall():
	keywords = row[0].split(",")
	for k in keywords:
		if k not in keywordCounts:
			keywordCounts[k] = 0
		keywordCounts[k] += 1

fiveBestKeywords = heapq.nlargest(5, keywordCounts, key=keywordCounts.get)
print fiveBestKeywords
for k in fiveBestKeywords:
	print keywordCounts[k]