-- Update currency setting from Rial to Afghani
UPDATE system 
SET setting_value = 'افغانی' 
WHERE record_type = 'setting' 
AND setting_key = 'currency';
