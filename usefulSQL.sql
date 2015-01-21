-- number of direct children for an element
SELECT cat.*, (SELECT COUNT(*) FROM wg_category AS chi WHERE chi.parent=cat.id) AS count FROM `wg_category` AS cat ORDER BY count DESC

-- GROUP BY distance
SELECT distance, COUNT(*) AS count FROM `wg_category` GROUP BY distance

-- number of fields per category
SELECT *, (char_length(fields)-char_length(replace(fields, '|', ''))+1) AS nbFields FROM `wg_category` ORDER BY (char_length(fields)-char_length(replace(fields, '|', ''))+1) DESC