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
	badClasses = ['hatnote', 'stub', 'ambox', 'vertical-navbox', 'thumb', 'infobox', 'navbox', 'haudio', 'reference']

	if child.name is not None and child.name == 'div' and child.get('id') is not None and child.get('id') == 'toc':
		return False
	if child.name is not None and (child.name == 'h2'):
		return False
	if child.get('class') is not None and any((True for x in badClasses if x in child.get('class'))):
		return False
	return True

def buildSummary(soup, whereToSave):
	# if not os.path.isfile(whereToSave):
	[x.extract() for x in soup.findAll(class_='reference')]
	content = ''
	for child in soup.children:
		if child.name is not None:
			if isBreak(child):
				break;
			if isValid(child):
				content += (child.get_text()+'\n').encode('ascii', 'ignore')
	content = content.replace('\n', '')
	f = open(whereToSave, 'w'); f.write(content); f.close();

# cur.execute("SELECT id FROM page ORDER BY PR DESC")
# i = 0
# for row in cur.fetchall():
# 	artId = row[0]
# 	getContent(artId)
# 	i += 1
# 	print artId, " / ", i