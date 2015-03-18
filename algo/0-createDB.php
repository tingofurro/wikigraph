<?php
/*
OBJECTIVE:
Once a Database named 'wikigraph' is created in the local mysql,
This script will create the main tables needed for further scripts
*/

include_once('../dbco.php');

// wg_category : Storing categories found
mysql_query("CREATE TABLE IF NOT EXISTS `wg_category` (`id` int(100) NOT NULL AUTO_INCREMENT, `name` text NOT NULL, `fields` text NOT NULL, `parent` int(100) NOT NULL, `distance` int(10) NOT NULL, `killBranch` int(10) NOT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;");

// wg_catlink : edges of the category graph/tree
mysql_query("CREATE TABLE IF NOT EXISTS `wg_catlink` (`id` int(11) NOT NULL AUTO_INCREMENT, `catfrom` int(11) NOT NULL, `catto` int(11) NOT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;");

// wg_page : wikipedia articles relevant to our subject
mysql_query("CREATE TABLE IF NOT EXISTS `wg_page` (`id` int(11) NOT NULL AUTO_INCREMENT, `name` text NOT NULL, `category` int(11) NOT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;");

// wg_link : links between two articles to create a graph
mysql_query("CREATE TABLE IF NOT EXISTS `wg_link` (`id` int(100) NOT NULL AUTO_INCREMENT, `from` int(100) NOT NULL, `to` int(100) NOT NULL, `type` int(100) NOT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;");

// wg_field : what are the main "fields" of mathematics
mysql_query("CREATE TABLE IF NOT EXISTS `wg_field` (`id` int(11) NOT NULL AUTO_INCREMENT, `name` text NOT NULL, `sname` text NOT NULL, `page` int(11) NOT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;");

// wg_subfield : we see what subfields are in each field. Eg: Algebra should have subfield: "Group Theory", "Ring Theory", "Fields" ... 
mysql_query("CREATE TABLE IF NOT EXISTS `wg_subfield` (`id` int(11) NOT NULL AUTO_INCREMENT, `field` int(11) NOT NULL, `name` text NOT NULL, `sname` text NOT NULL, `page` int(11) NOT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;");
?>