from bs4 import BeautifulSoup
from dbco import *
import urllib, os.path

def loadBlackList(root):
	f = open('blacklist/'+root+'.txt')
	st = f.read()
	f.close()
	return st.splitlines()
def isGood(catName):
	catLower = catName.lower()
	if 'stub' not in catLower and 'books' not in catLower and 'history' not in catLower and 'software' not in catLower and 'commons' not in catLower:
		return True
	return False

parentId = 0
root = 'Fields_of_mathematics'
categoryList = [root]
toSearch = []
cur.execute("TRUNCATE TABLE `category`")
blackList = loadBlackList(root)
toSearch.append([root, 0, 0])
dbPush = []

while len(toSearch) > 0:
	me = toSearch[0]
	level = me[1]
	parent = me[2]
	dbPush.append("(NULL, \""+me[0]+"\", '"+str(parent)+"', '"+str(level)+"')")
	parentId += 1
	print level
	html_doc = ''
	if os.path.isfile('cache/'+me[0]+'.txt'):
		f = open('cache/'+me[0]+'.txt')
		html_doc = f.read()
		f.close()
	else:
		f = urllib.urlopen("https://en.wikipedia.org/wiki/Category:"+me[0])
		html_doc = f.read()
		f2 = open('cache/'+me[0]+'.txt', 'w')
		f2.write(html_doc)
		f2.close()
		f.close()
	toFind = '/wiki/Category:'
	soup = BeautifulSoup(html_doc, 'html.parser')
	newDiscover = []
	for link in soup.find_all('a'):
		linkStr = link.get('href')
		if linkStr is not None and linkStr[:len(toFind)] == toFind:
			catName = linkStr[len(toFind):]
			if isGood(catName) and catName not in categoryList and catName not in blackList and level < 6:
				newDiscover.append([catName, level+1, parentId])
				categoryList.append(catName)
	if parentId%50 == 0:
		cur.execute("INSERT INTO `category` (`id`, `name`, `parent`, `level`) VALUES "+", ".join(dbPush)+';')
		dbPush = []
	toSearch.pop(0)
	toSearch.extend(newDiscover)
if len(dbPush) > 0:
	cur.execute("INSERT INTO `category` (`id`, `name`, `parent`, `level`) VALUES "+", ".join(dbPush)+';')
	dbPush = []	