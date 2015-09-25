import MySQLdb
import sys

db = MySQLdb.connect(host="localhost", user="root", passwd="wikigraph", db="wikigraph")
db.autocommit(True)
cur = db.cursor()