from dbco import *

neighbors = {}
cur.execute("SELECT `from`, `to` FROM link")
for e in cur.fetchall():
	if str(e[0]) not in neighbors:
		neighbors[str(e[0])] = set([])
	neighbors[str(e[0])].add(str(e[1]))

cur.execute("UPDATE page SET badPage=0 WHERE badPage=2") # reset

cur.execute("SELECT id, name FROM category WHERE level>=2 ORDER BY id")
for cat in cur.fetchall():
	category = cat[0]
	cur.execute("SELECT id, category FROM page WHERE category<="+str(category))
	pages = cur.fetchall()
	allPages = set([str(p[0]) for p in pages])
	inPages = set([str(p[0]) for p in pages if p[1] == category])
	if len(inPages) > 0:
		inEdgeCount = 0; totEdgeCount = 0;
		for p in inPages:
			if p in neighbors:
				nei = neighbors[p]
				inEdgeCount += len(nei & inPages); totEdgeCount += len(nei & allPages)
		inFactor = inEdgeCount/float(totEdgeCount+1.0)
		if inFactor >= 0.5:
			cur.execute("UPDATE page SET badPage=2 WHERE id IN ("+','.join(inPages)+")")
			print cat[1], "-> ", inFactor