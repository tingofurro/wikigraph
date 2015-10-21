from dbco import *
from igraph import *
import sys

if len(sys.argv) > 1:
	prefix = sys.argv[1]+'_'
	try:
		cur.execute("ALTER TABLE `"+prefix+"page` ADD `PR` FLOAT( 20, 3 ) NOT NULL ")
	except:
		pass

	cur.execute("SELECT id FROM "+prefix+"page ORDER BY id")
	pages = [str(row[0]) for row in cur.fetchall()]

	g = Graph(directed=True); g.add_vertices(len(pages))

	cur.execute("SELECT `from`, `to`, id FROM "+prefix+"link ORDER BY id")
	g.add_edges([(row[0]-1, row[1]-1) for row in cur.fetchall()])

	PR = g.pagerank()

	query = "UPDATE "+prefix+"page SET PR = CASE id "
	for page, p in zip(pages, PR):
		query += 'WHEN '+page+' THEN '+str(len(pages)*p)+' '
	query += 'END WHERE id IN('+','.join(pages)+')'
	cur.execute(query)
else:
	print "Enter the table prefix: 'ee'"