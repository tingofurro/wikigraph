from dbco import *
from bs4 import BeautifulSoup

nameToId = {}

cur.execute("SELECT id, name FROM  page ORDER BY id");

for row in cur.fetchall():
	nameToId[row[1]] = row[0]

cur.execute("SELECT fromName, toName FROM wg_redirect ORDER BY id")
for row in cur.fetchall():
	if row[1] in nameToId:
		nameToId[row[0]] = nameToId[row[1]]

print "Loaded all pages"

dbPush = [];

cur.execute("SELECT `from` FROM link ORDER BY `from` DESC LIMIT 1")
res = cur.fetchall()
if len(res) > 0:
	firstId = res[0][0]
else:
	firstId = 0

cur.execute("SELECT id FROM page WHERE id>"+str(firstId)+" ORDER BY id")
for row in cur.fetchall():
	myTos = []
	f = open('html/'+str(row[0])+'.html')
	soup = BeautifulSoup(f.read(), 'lxml')
	f.close()
	toFind = '/wiki/'
	if soup != None:
		for link in soup.find_all('a'):
			linkStr = link.get('href')
			if linkStr is not None and linkStr[:len(toFind)] == toFind:
				pageName = linkStr[len(toFind):]
				if ':' not in pageName and pageName in nameToId and nameToId[pageName] not in myTos:
					dbPush.append("(NULL, \""+str(row[0])+"\", '"+str(nameToId[pageName])+"')")
					myTos.append(nameToId[pageName])
	if len(dbPush) > 5000:
		cur.execute("INSERT INTO `link` (`id`, `from`, `to`) VALUES "+", ".join(dbPush)+';')
		dbPush = []
	print row[0]

if len(dbPush) > 0:
	cur.execute("INSERT INTO `link` (`id`, `from`, `to`) VALUES "+", ".join(dbPush)+';')