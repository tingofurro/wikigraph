#!/usr/bin/env python
# -*- coding: utf-8 -*-
from bs4 import BeautifulSoup
import urllib, os.path
from crawlContent import *

badMathCats = ['Applied_mathematics', 'Computational_mathematics', 'Dynamical_systems', 'Experimental_mathematics', 'Foundations_of_mathematics', 'Elementary_mathematics', 'Recreational_mathematics']


badMedCats = ['Medical_lists', 'Medical_activism‎', 'Medical_associations', 'Health_insurance', 'Religion_and_medicine', 'Medicine_stubs']

def findSubCategories(soup):
	# Given a /wiki/Category:cat page
	children = []; soup1 = soup.find(id='mw-subcategories')
	toFind = '/wiki/Category:'
	if soup1 != None:
		for link in soup1.find_all('a'):
			linkStr = link.get('href')
			if linkStr is not None and linkStr[:len(toFind)] == toFind:
				c = linkStr[len(toFind):]
				if c not in badMathCats and c not in badMedCats:
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

def isBreakSummary(child): # should we stop there
	if child.name is not None and child.name == 'div' and child.get('id') is not None and child.get('id') == 'toc':
		return True
	if child.name is not None and (child.name == 'h2'):
		return True
	return False
def isValidSummary(child):
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

def buildSummary(pageName, whereToSave):
	if not os.path.isfile(whereToSave):
		soup = BeautifulSoup(loadPage(pageName), 'lxml');
		[x.extract() for x in soup.findAll(class_='reference')]
		content = ''
		for child in soup.children:
			if child.name is not None:
				if isBreakSummary(child):
					break;
				if isValidSummary(child):
					cont = ''
					try:
						cont = child.get_text()
					except:
						pass
					content += (cont+'\n').encode('ascii', 'ignore')
		content = content.replace('\n', '')
		f = open(whereToSave, 'w'); f.write(content); f.close();