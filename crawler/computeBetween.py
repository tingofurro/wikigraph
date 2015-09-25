from dbco import *
from igraph import *

cur.execute("SELECT id FROM page")
pages = [row[0] for row in cur.fetchall()]

g = Graph(directed=True)
g.add_vertices(len(pages))

cur.execute("SELECT `from`, `to`, id FROM link ORDER BY id"); rows = cur.fetchall();
allEdges = [(int(r[0])-1, int(r[1])-1) for r in rows]

g.add_edges(allEdges)

betweenness = g.betweenness()

pString = [str(p) for p in pages]

query = "UPDATE page SET betweenness = CASE id "
for page, b in zip(pages, betweenness):
	query += 'WHEN '+str(page)+' THEN '+str(b)+' '
query += 'END WHERE id IN('+','.join(pString)+')'
cur.execute(query)