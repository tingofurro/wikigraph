from dbco import *

def build_graph(arts, fileName):
	artsList = ','.join(arts)
	cur.execute("SELECT `from`, `to` FROM link WHERE `to` IN ("+artsList+") AND `from` IN ("+artsList+")")
	f = open(fileName,'w'); [f.write(str(row[0])+' '+str(row[1])+'\n') for row in cur.fetchall()]; f.close()