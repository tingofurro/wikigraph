SELECT cat.*, (SELECT COUNT(*) FROM wg_category AS chi WHERE chi.parent=cat.id) AS count FROM `wg_category` AS cat ORDER BY count DESC
SELECT distance, COUNT(*) AS count FROM `wg_category` GROUP BY distance