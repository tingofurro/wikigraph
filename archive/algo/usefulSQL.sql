-- number of direct children for an element
SELECT parent, COUNT( * ) AS count FROM `wg_category` GROUP BY parent ORDER BY `count` DESC

-- GROUP BY distance
SELECT distance, COUNT(*) AS count FROM `wg_category` GROUP BY distance

-- number of fields per category
SELECT *, (char_length(fields)-char_length(replace(fields, '|', ''))+1) AS nbFields FROM `wg_category` ORDER BY (char_length(fields)-char_length(replace(fields, '|', ''))+1) DESC

-- see links with page names
SELECT link.*, fromTab.name AS fromName, toTab.name AS toName FROM `wg_link` AS link INNER JOIN wg_page AS fromTab ON fromTab.id=link.`from` INNER JOIN wg_page AS toTab ON toTab.id=link.`to`

-- how many are visited vs. non visited
SELECT visited, COUNT(*) AS count FROM `wg_page` GROUP BY visited

-- Any double links
SELECT *, (0.5*(`to`+`from`)*(`to`+`from`+1)+`to`) AS cantor, COUNT(*) AS count FROM `wg_link` GROUP BY (0.5*(`to`+`from`)*(`to`+`from`+1)+`to`) ORDER BY COUNT(*) DESC

-- Links grouped by `from`
SELECT link.*, COUNT(*) AS count, page.name FROM wg_link AS link INNER JOIN wg_page AS page ON page.id=link.`from` GROUP BY `from` ORDER BY COUNT(*) DESC

-- Make sense of topics
SELECT top.*, pa.name FROM `wg_topic` AS top INNER JOIN wg_page AS pa ON top.page=pa.id WHERE top.PR>=2 ORDER BY top.`field` ASC, top.`topic` ASC, top.PR DESC