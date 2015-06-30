WikiGraph - Setup
=========

Prereqs
=========
Apache
PHP (Version 5.3 at least)
PHP APC Extension (For the isMathematician, isSoftware algorithms)
MySQL

What is this
=========

Once in the right folder (www)
You can call these scripts in that order to:
- Crawl Wikipedia for Categories
- Crawl the pages
- Clean their HTML and get text
- extract the links of the pages to create the graph
- Calculate PageRank

Disclaimer
==========
These scripts might not work as is, you need to put them in root folder (they are in /setup/) right now.
Some files might need you to setup tables in a wikigraph table. Sorry about that.