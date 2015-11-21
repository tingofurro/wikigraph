from dbco import *

cur.execute("SELECT id FROM ma_page ORDER BY PR DESC LIMIT 4000")

pages = [str(p[0]) for p in cur.fetchall()]
i = 1
pageIndex = {}
for p in pages:
	pageIndex[p] = str(i); i += 1
cur.execute("SELECT `from`, `to` FROM ma_link WHERE `from` IN ("+",".join(pages)+") AND `to` IN ("+",".join(pages)+")")
f = open('matrix.dat', 'w')
for e in cur.fetchall():
	f.write(pageIndex[str(e[0])]+'\t'+pageIndex[str(e[1])]+'\t1\n')
	f.write(pageIndex[str(e[1])]+'\t'+pageIndex[str(e[0])]+'\t1\n')
