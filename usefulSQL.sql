-- number of direct children for an element
SELECT cat.*, (SELECT COUNT(*) FROM wg_category AS chi WHERE chi.parent=cat.id) AS count FROM `wg_category` AS cat ORDER BY count DESC

-- GROUP BY distance
SELECT distance, COUNT(*) AS count FROM `wg_category` GROUP BY distance

-- number of fields per category
SELECT *, (char_length(fields)-char_length(replace(fields, '|', ''))+1) AS nbFields FROM `wg_category` ORDER BY (char_length(fields)-char_length(replace(fields, '|', ''))+1) DESC

-- see links with page names
SELECT link.*, fromTab.name AS fromName, toTab.name AS toName FROM `wg_links` AS link INNER JOIN wg_page AS fromTab ON fromTab.id=link.`from` INNER JOIN wg_page AS toTab ON toTab.id=link.`to`

-- how many are visited vs. non visited
SELECT visited, COUNT(*) AS count FROM `wg_page` GROUP BY visited