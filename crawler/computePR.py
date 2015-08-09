from dbco import *
from igraph import *

cur.execute("SELECT id FROM page")
pages = []
for row in cur.fetchall():
	pages.append(row[0])

g = Graph(directed=True)
g.add_vertices(len(pages))

cur.execute("SELECT `from`, `to`, id FROM link ORDER BY id")
allEdges = []
for row in cur.fetchall():
	toNode = int(row[1])
	fromNode = int(row[0])
	allEdges.append((fromNode-1, toNode-1))
	if row[2]%1000 == 0:
		g.add_edges(allEdges)
		allEdges = [];

g.add_edges(allEdges)

PR = g.pagerank()

pString = []
for p in pages:
	pString.append(str(p))

query = "UPDATE page SET PR = CASE id "
for page, p in zip(pages, PR):
	query += 'WHEN '+str(page)+' THEN '+str(len(pages)*p)+' '
query += 'END WHERE id IN('+','.join(pString)+')'
cur.execute(query)