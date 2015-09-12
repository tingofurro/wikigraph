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
	if child is None:
		return False
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
				cont = ''
				try:
					cont = child.get_text()
				except:
					pass

				content += (cont+'\n').encode('ascii', 'ignore')
	content = content.replace('\n', '')
	f = open(whereToSave, 'w'); f.write(content); f.close();

def buildHTMLSummary(soup):
	soup = soup.find('body')
	content = ''
	alreadyBroke = False
	for child in soup.children:
		if alreadyBroke:
			child.extract()
		elif child.name is not None:
			if isBreak(child):
				alreadyBroke = True
			if not isValid(child):
				child.extract()
	return soup