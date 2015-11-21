import MySQLdb
import sys

db = MySQLdb.connect(host="127.0.0.1", user="root", passwd="wikigraph", db="wikigraph")
db.autocommit(True)
cur = db.cursor()