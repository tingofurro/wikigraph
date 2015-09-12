from bs4 import BeautifulSoup
from dbco import *
import urllib, os.path

pageList = []
dbPush = []
cur.execute("TRUNCATE TABLE `page`");
cur.execute("SELECT id, name FROM category ORDER BY id")
for row in cur.fetchall():
	name = row[1]
	idCat = row[0]
	html_doc = ''
	whereToSave = 'category/'+name+'.txt'

	if os.path.isfile(whereToSave):
		f = open(whereToSave); html_doc = f.read(); f.close();
	else:
		f = urllib.urlopen("https://en.wikipedia.org/wiki/Category:"+name); html_doc = f.read(); f.close();
		f2 = open(whereToSave, 'w'); f2.write(html_doc); f2.close();
	toFind = '/wiki/'
	soup = BeautifulSoup(html_doc, 'lxml')
	soup = soup.find(id='mw-pages')
	newDiscover = []
	if soup != None:
		for link in soup.find_all('a'):
			linkStr = link.get('href')
			if linkStr is not None and linkStr[:len(toFind)] == toFind:
				pageName = linkStr[len(toFind):]
				if ':' not in pageName and '/' not in pageName and pageName[:len(listPageStr)] != listPageStr and pageName not in pageList:
					dbPush.append('(NULL, "'+pageName+'", '+str(idCat)+', "")')
					pageList.append(pageName)
	if len(dbPush)>100:
		cur.execute("INSERT INTO `page` (`id`, `name`, `category`, `keywords`) VALUES "+", ".join(dbPush)+';')
		dbPush = []
	print idCat
if len(dbPush) > 0:
	cur.execute("INSERT INTO `page` (`id`, `name`, `category`, `keywords`) VALUES "+", ".join(dbPush)+';')
	dbPush = []