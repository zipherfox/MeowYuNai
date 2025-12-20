-- Password Fix for Lost Cat Tracker
-- Run this if you're having login issues

USE lost_cat_tracker;

-- The password hash in the original mock data was for 'admin123'
-- This script fixes passwords for all test accounts

-- Fix password for all Bangkok test users to 'bangkok123'
UPDATE users SET password = '$2y$10$AqF71AAD3yTGFA/jRM..lOk0rQZs4EpGNWATpWzpx3Xxv.Ct5ZBGW' WHERE username = 'somchai_p';
UPDATE users SET password = '$2y$10$AqF71AAD3yTGFA/jRM..lOk0rQZs4EpGNWATpWzpx3Xxv.Ct5ZBGW' WHERE username = 'nisa_w';
UPDATE users SET password = '$2y$10$AqF71AAD3yTGFA/jRM..lOk0rQZs4EpGNWATpWzpx3Xxv.Ct5ZBGW' WHERE username = 'praphan_s';
UPDATE users SET password = '$2y$10$AqF71AAD3yTGFA/jRM..lOk0rQZs4EpGNWATpWzpx3Xxv.Ct5ZBGW' WHERE username = 'araya_k';
UPDATE users SET password = '$2y$10$AqF71AAD3yTGFA/jRM..lOk0rQZs4EpGNWATpWzpx3Xxv.Ct5ZBGW' WHERE username = 'thanawat_r';
UPDATE users SET password = '$2y$10$AqF71AAD3yTGFA/jRM..lOk0rQZs4EpGNWATpWzpx3Xxv.Ct5ZBGW' WHERE username = 'kulap_m';
UPDATE users SET password = '$2y$10$AqF71AAD3yTGFA/jRM..lOk0rQZs4EpGNWATpWzpx3Xxv.Ct5ZBGW' WHERE username = 'prasert_c';
UPDATE users SET password = '$2y$10$AqF71AAD3yTGFA/jRM..lOk0rQZs4EpGNWATpWzpx3Xxv.Ct5ZBGW' WHERE username = 'siriporn_t';
UPDATE users SET password = '$2y$10$AqF71AAD3yTGFA/jRM..lOk0rQZs4EpGNWATpWzpx3Xxv.Ct5ZBGW' WHERE username = 'wanchai_n';
UPDATE users SET password = '$2y$10$AqF71AAD3yTGFA/jRM..lOk0rQZs4EpGNWATpWzpx3Xxv.Ct5ZBGW' WHERE username = 'malee_p';
UPDATE users SET password = '$2y$10$AqF71AAD3yTGFA/jRM..lOk0rQZs4EpGNWATpWzpx3Xxv.Ct5ZBGW' WHERE username = 'kittipong_l';
UPDATE users SET password = '$2y$10$AqF71AAD3yTGFA/jRM..lOk0rQZs4EpGNWATpWzpx3Xxv.Ct5ZBGW' WHERE username = 'pawina_s';
UPDATE users SET password = '$2y$10$AqF71AAD3yTGFA/jRM..lOk0rQZs4EpGNWATpWzpx3Xxv.Ct5ZBGW' WHERE username = 'anon_k';
UPDATE users SET password = '$2y$10$AqF71AAD3yTGFA/jRM..lOk0rQZs4EpGNWATpWzpx3Xxv.Ct5ZBGW' WHERE username = 'ratana_w';
UPDATE users SET password = '$2y$10$AqF71AAD3yTGFA/jRM..lOk0rQZs4EpGNWATpWzpx3Xxv.Ct5ZBGW' WHERE username = 'surasak_b';
UPDATE users SET password = '$2y$10$AqF71AAD3yTGFA/jRM..lOk0rQZs4EpGNWATpWzpx3Xxv.Ct5ZBGW' WHERE username = 'atchara_m';
UPDATE users SET password = '$2y$10$AqF71AAD3yTGFA/jRM..lOk0rQZs4EpGNWATpWzpx3Xxv.Ct5ZBGW' WHERE username = 'chatchai_p';
UPDATE users SET password = '$2y$10$AqF71AAD3yTGFA/jRM..lOk0rQZs4EpGNWATpWzpx3Xxv.Ct5ZBGW' WHERE username = 'suda_t';
UPDATE users SET password = '$2y$10$AqF71AAD3yTGFA/jRM..lOk0rQZs4EpGNWATpWzpx3Xxv.Ct5ZBGW' WHERE username = 'narit_s';
UPDATE users SET password = '$2y$10$AqF71AAD3yTGFA/jRM..lOk0rQZs4EpGNWATpWzpx3Xxv.Ct5ZBGW' WHERE username = 'apinya_w';

-- Keep admin password as 'admin123'
UPDATE users SET password = '$2y$10$8UhVs679sLq4Qwx6NCl9gO6fNl72MfhH6w3aKWSAFG5lGkyGjoA1W' WHERE username = 'admin';
-- Show updated users
SELECT username, email, 
       CASE 
           WHEN username = 'admin' THEN 'admin123'
           ELSE 'bangkok123'
       END as password_text
FROM users
ORDER BY id;

SELECT 'All passwords have been updated!' as status;
