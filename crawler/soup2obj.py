from bs4 import BeautifulSoup
import urllib, os.path
from crawlContent import *

badMathCats = ['Applied_mathematics', 'Computational_mathematics', 'Dynamical_systems', 'Experimental_mathematics', 'Foundations_of_mathematics', 'Elementary_mathematics', 'Recreational_mathematics']	

def findSubCategories(soup):
	# Given a /wiki/Category:cat page
	children = []; soup1 = soup.find(id='mw-subcategories')
	toFind = '/wiki/Category:'
	if soup1 != None:
		for link in soup1.find_all('a'):
			linkStr = link.get('href')
			if linkStr is not None and linkStr[:len(toFind)] == toFind:
				c = linkStr[len(toFind):]
				if c not in badMathCats:
					children.append(c)
	return children
def findPages(soup):
	# Given a /wiki/Category:cat page
	pageList = []; soup2 = soup.find(id='mw-pages')
	toFind = '/wiki/'
	if soup2 != None:
		for link in soup2.find_all('a'):
			linkStr = link.get('href')
			if linkStr is not None and linkStr[:len(toFind)] == toFind:
				pageName = linkStr[len(toFind):]
				if ':' not in pageName and '/' not in pageName:
					pageList.append(pageName)
	return pageList

def loadPage(pageName):
	whereToSave = 'html/'+pageName.replace('/', '')+'.html'
	if not os.path.isfile(whereToSave):
		f = urllib.urlopen("https://en.wikipedia.org/wiki/"+pageName); html_doc = f.read(); f.close();
		soup = BeautifulSoup(html_doc, 'lxml'); innerSoup = soup.find(id='mw-content-text')
		listBadIds = ['siteNotice']
		cleanHTML = ''
		skipping = False
		for elem in innerSoup.contents:
			if elem.name != None:
				skipping = isSkipSection(elem, skipping)
				if not skipping and isValid(elem):
					cleanHTML += elem.prettify()
		f = open(whereToSave, 'w'); f.write(cleanHTML); f.close();
	else:
		f = open(whereToSave); cleanHTML = f.read(); f.close();
	return cleanHTML

def pageLinks(name):
	# Given a /wiki/page
	whereToSave = 'pageLinks/'+name.replace('/', '')+'.html'
	if os.path.isfile(whereToSave):
		f = open(whereToSave); links = f.read().split('\n'); f.close();
	else:
		soup = BeautifulSoup(loadPage(name), 'lxml');
		links = set([])
		toFind = '/wiki/'
		if soup != None:
			for link in soup.find_all('a'):
				linkStr = link.get('href')
				if linkStr is not None and linkStr[:len(toFind)] == toFind:
					pageName = linkStr[len(toFind):]
					if ':' not in pageName:
						links.add(pageName)
		f2 = open(whereToSave, 'w'); f2.write('\n'.join(list(links))); f2.close();
	return links