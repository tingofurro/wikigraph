from dbco import *
from bs4 import BeautifulSoup
import sys
import urllib, os.path

def isBreak(child): # should we stop there
	if child.name is not None and child.name == 'div' and child.get('id') is not None and child.get('id') == 'toc':
		return True
	if child.name is not None and (child.name == 'h2'):
		return True
	return False
def isValid(child):
	if child.name is not None and child.name == 'div' and child.get('id') is not None and child.get('id') == 'toc':
		return False
	if child.name is not None and (child.name == 'h2'):
		return False
	if child.get('class') is not None and 'hatnote' in child.get('class'):
		return False
	if child.get('class') is not None and 'stub' in child.get('class'):
		return False
	return True

def getContent(artId):
	if not os.path.isfile('summary2/'+str(artId)+'.txt'):
		f = open('../crawler/data/'+str(artId)+'.html')
		htm = f.read()
		soup = BeautifulSoup(htm, 'html.parser')
		f.close()
		content = ''
		for child in soup.children:
			if child.name is not None:
				if isBreak(child):
					break;
				if isValid(child):
					content += (child.get_text()+'\n').encode('ascii', 'ignore')
		f = open('summary2/'+str(artId)+'.txt', 'w')
		f.write(content)
		f.close()

cur.execute("SELECT id FROM page ORDER BY id")
for row in cur.fetchall():
	artId = row[0]
	getContent(artId)
	print artId