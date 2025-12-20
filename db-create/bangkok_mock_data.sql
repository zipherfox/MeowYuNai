-- Mock Data for Lost Cat Tracker - Bangkok, Thailand
-- This file adds realistic test data with Bangkok locations

USE lost_cat_tracker;

-- Clear existing data (except admin)
DELETE FROM sightings;
DELETE FROM lost_reports;
DELETE FROM cats;
DELETE FROM users WHERE id > 1;

-- Insert Bangkok Users (20 users)
INSERT INTO users (username, email, password, full_name, phone, address, latitude, longitude) VALUES
-- Password for all: bangkok123
('somchai_p', 'somchai.p@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Somchai Pattana', '08-1234-5678', '123 Sukhumvit Soi 11, Khlong Toei, Bangkok', 13.7377, 100.5617),
('nisa_w', 'nisa.w@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nisa Wong', '08-2345-6789', '456 Silom Road, Bang Rak, Bangkok', 13.7248, 100.5323),
('praphan_s', 'praphan.s@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Praphan Saetang', '08-3456-7890', '789 Ratchadaphisek Road, Huai Khwang, Bangkok', 13.7658, 100.5743),
('araya_k', 'araya.k@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Araya Komolmas', '08-4567-8901', '321 Rama IV Road, Pathum Wan, Bangkok', 13.7307, 100.5418),
('thanawat_r', 'thanawat.r@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Thanawat Rojana', '08-5678-9012', '555 Phaya Thai Road, Ratchathewi, Bangkok', 13.7563, 100.5328),
('kulap_m', 'kulap.m@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Kulap Manee', '08-6789-0123', '88 Wireless Road, Pathum Wan, Bangkok', 13.7408, 100.5432),
('prasert_c', 'prasert.c@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Prasert Chaiwong', '08-7890-1234', '777 Ekkamai Road, Khlong Toei, Bangkok', 13.7263, 100.5853),
('siriporn_t', 'siriporn.t@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Siriporn Thongchai', '08-8901-2345', '999 On Nut Road, Prawet, Bangkok', 13.7053, 100.6013),
('wanchai_n', 'wanchai.n@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Wanchai Narong', '08-9012-3456', '234 Sathorn Road, Yan Nawa, Bangkok', 13.7194, 100.5267),
('malee_p', 'malee.p@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Malee Prasong', '08-0123-4567', '456 Thonglor Soi 10, Watthana, Bangkok', 13.7338, 100.5734),
('kittipong_l', 'kittipong.l@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Kittipong Lertsak', '09-1234-5678', '678 Ramkhamhaeng Road, Saphan Sung, Bangkok', 13.7633, 100.6143),
('pawina_s', 'pawina.s@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Pawina Srisuk', '09-2345-6789', '890 Lat Phrao Road, Chatuchak, Bangkok', 13.8158, 100.5615),
('anon_k', 'anon.k@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Anon Kasem', '09-3456-7890', '123 Phahonyothin Road, Chatuchak, Bangkok', 13.8003, 100.5497),
('ratana_w', 'ratana.w@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ratana Wattana', '09-4567-8901', '345 Rama IX Road, Huai Khwang, Bangkok', 13.7594, 100.5667),
('surasak_b', 'surasak.b@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Surasak Bunyasarn', '09-5678-9012', '567 Charoen Krung Road, Bang Rak, Bangkok', 13.7233, 100.5145),
('atchara_m', 'atchara.m@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Atchara Maneerat', '09-6789-0123', '789 Petchburi Road, Ratchathewi, Bangkok', 13.7508, 100.5413),
('chatchai_p', 'chatchai.p@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Chatchai Preecha', '09-7890-1234', '901 Asoke Road, Khlong Toei, Bangkok', 13.7373, 100.5603),
('suda_t', 'suda.t@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Suda Thepsutin', '09-8901-2345', '234 Phra Ram 2 Road, Bang Khun Thian, Bangkok', 13.6488, 100.4667),
('narit_s', 'narit.s@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Narit Somboon', '09-9012-3456', '456 Pinklao Road, Bangkok Noi, Bangkok', 13.7783, 100.4897),
('apinya_w', 'apinya.w@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Apinya Wongsa', '09-0123-4567', '678 Bangna-Trad Road, Bang Na, Bangkok', 13.6687, 100.6033);

-- Insert Cats (35 cats with Thai-inspired names)
INSERT INTO cats (user_id, name, breed, color, age, gender, description, microchip_id, distinctive_features) VALUES
-- User 2 cats
(2, 'Luna', 'Siamese', 'Cream with brown points', 3, 'female', 'Very vocal and friendly. Loves to follow people around. Blue almond-shaped eyes.', '982000123456789', 'Dark brown face mask, blue eyes, white paws'),
(2, 'Mochi', 'Persian', 'White', 2, 'male', 'Fluffy and calm. Indoor cat who is very shy with strangers.', '982000123456790', 'Long fluffy coat, copper eyes, pink nose'),

-- User 3 cats
(3, 'Simba', 'Orange Tabby', 'Orange with white', 4, 'male', 'Playful and energetic. Likes to climb trees. Very friendly with everyone.', '982000123456791', 'Orange stripes, white chest and paws, pink nose'),
(3, 'Nala', 'Calico', 'Orange, black, and white', 5, 'female', 'Sweet and gentle. Loves being petted. Mostly stays indoors.', '982000123456792', 'Tri-color pattern, white chest, orange patches on face'),

-- User 4 cats
(4, 'Tiger', 'Bengal', 'Brown spotted', 2, 'male', 'Very active and athletic. Loves to jump high places. Slightly aggressive with other cats.', '982000123456793', 'Leopard-like spots, muscular build, green eyes'),
(4, 'Shadow', 'Black Domestic Shorthair', 'Pure black', 6, 'male', 'Mysterious and independent. Nocturnal. Yellow-green eyes.', '982000123456794', 'Sleek black coat, yellow eyes, white whiskers'),

-- User 5 cats
(5, 'Daisy', 'Scottish Fold', 'Gray', 1, 'female', 'Young and playful. Folded ears. Very curious about everything.', '982000123456795', 'Folded ears, round face, gray coat, golden eyes'),
(5, 'Max', 'Maine Coon', 'Brown tabby', 7, 'male', 'Large and gentle giant. Very friendly. Long fluffy tail.', '982000123456796', 'Very large size, tufted ears, bushy tail, brown stripes'),

-- User 6 cats
(6, 'Bella', 'Russian Blue', 'Blue-gray', 4, 'female', 'Elegant and reserved. Silvery coat. Green eyes. Indoor only.', '982000123456797', 'Silver-blue coat, bright green eyes, small frame'),
(6, 'Cookie', 'British Shorthair', 'Gray', 3, 'male', 'Chunky and calm. Round face. Very food motivated.', '982000123456798', 'Round chubby face, dense gray coat, copper eyes'),

-- User 7 cats
(7, 'Milo', 'Orange Domestic', 'Ginger', 2, 'male', 'Adventurous outdoor cat. Very friendly. Loves to explore.', '982000123456799', 'Solid ginger color, white chin, freckles on nose'),
(7, 'Chloe', 'Tuxedo', 'Black and white', 5, 'female', 'Elegant black and white pattern. Very vocal. Indoor cat.', '982000123456800', 'Black coat with white chest, paws, and face marking'),

-- User 8 cats
(8, 'Oliver', 'Ragdoll', 'Blue point', 3, 'male', 'Very relaxed and floppy. Blue eyes. Loves being held.', '982000123456801', 'Blue-gray points, bright blue eyes, large size, fluffy'),

-- User 9 cats
(9, 'Lucy', 'Tortoiseshell', 'Black and orange', 4, 'female', 'Sassy personality. Unique coloring. Very attached to owner.', '982000123456802', 'Mottled black and orange pattern, amber eyes'),
(9, 'Charlie', 'American Shorthair', 'Silver tabby', 6, 'male', 'Calm and friendly. Good with children. Green eyes.', '982000123456803', 'Silver with black stripes, muscular build'),

-- User 10 cats
(10, 'Lily', 'White Persian', 'Pure white', 2, 'female', 'Fluffy white princess. Requires daily grooming. Blue eyes.', '982000123456804', 'Long white coat, flat face, blue eyes, pink nose'),

-- User 11 cats
(11, 'Leo', 'Abyssinian', 'Ruddy', 3, 'male', 'Very active and intelligent. Reddish-brown ticked coat.', '982000123456805', 'Ticked ruddy coat, almond eyes, large ears, athletic'),

-- User 12 cats
(12, 'Zoe', 'Sphynx', 'Gray', 2, 'female', 'Hairless with wrinkled skin. Very warm to touch. Indoor only.', '982000123456806', 'Hairless, wrinkled gray skin, large ears, lemon eyes'),
(12, 'Jasper', 'Norwegian Forest Cat', 'Brown tabby', 5, 'male', 'Large fluffy cat. Loves cold weather. Very friendly.', '982000123456807', 'Long coat, tufted ears, bushy tail, triangular face'),

-- User 13 cats
(13, 'Sophie', 'Birman', 'Seal point', 3, 'female', 'White paws like wearing socks. Blue eyes. Gentle nature.', '982000123456808', 'White gloves on paws, blue eyes, seal points'),

-- User 14 cats
(14, 'Rocky', 'Exotic Shorthair', 'Orange', 4, 'male', 'Flat-faced like Persian but short hair. Very lazy and cuddly.', '982000123456809', 'Flat face, short dense coat, large round eyes'),
(14, 'Misty', 'Devon Rex', 'Gray', 2, 'female', 'Curly coat, large ears. Very playful and mischievous.', '982000123456810', 'Curly soft coat, oversized ears, slender body'),

-- User 15 cats
(15, 'Felix', 'Black and White Tuxedo', 'Black and white', 7, 'male', 'Senior cat, very calm. Black with white tuxedo markings.', '982000123456811', 'Perfect tuxedo marking, white mustache, green eyes'),

-- User 16 cats
(16, 'Luna', 'Gray Tabby', 'Gray striped', 1, 'female', 'Young kitten. Very playful and energetic. Still learning.', '982000123456812', 'Gray stripes, white paws and chest, green eyes'),
(16, 'Oscar', 'Brown Tabby', 'Brown striped', 5, 'male', 'Large and muscular. Outdoor hunter. Brown mackerel stripes.', '982000123456813', 'Brown stripes, white chin, yellow eyes, large paws'),

-- User 17 cats
(17, 'Princess', 'Himalayan', 'Chocolate point', 4, 'female', 'Long-haired color point. Very pampered. Indoor only.', '982000123456814', 'Long coat, chocolate points, blue eyes, flat face'),

-- User 18 cats
(18, 'Buddy', 'Mixed Breed', 'Orange and white', 3, 'male', 'Friendly neighborhood cat. Loves everyone. Very social.', '982000123456815', 'Orange patches, white base, friendly expression'),
(18, 'Mittens', 'White Domestic', 'White with gray', 6, 'female', 'White with gray patches. Very clean. Indoor cat.', '982000123456816', 'Mostly white, gray patches on head, green eyes'),

-- User 19 cats
(19, 'Ginger', 'Red Tabby', 'Ginger', 2, 'female', 'Rare ginger female. Very sweet. Loves to cuddle.', '982000123456817', 'Solid ginger, white paws, pink nose'),

-- User 20 cats
(20, 'Whiskers', 'Gray and White', 'Gray and white', 8, 'male', 'Senior cat with long whiskers. Calm and wise. Very gentle.', '982000123456818', 'Long whiskers, gray back, white belly, yellow eyes'),
(20, 'Smokey', 'Chartreux', 'Blue-gray', 5, 'male', 'Dense blue-gray coat. Copper eyes. Quiet and sweet.', '982000123456819', 'Thick blue-gray coat, copper eyes, smiling expression'),

-- User 21 cats
(21, 'Patches', 'Calico', 'Orange, black, white', 4, 'female', 'Classic calico pattern. Very independent but affectionate.', '982000123456820', 'Distinct patches of orange and black on white'),
(21, 'Boots', 'Black with white paws', 'Black and white', 3, 'male', 'Black cat with white paws like boots. Very playful.', '982000123456821', 'Black body, four white paws, white chest spot'),

-- Additional cats for variety
(3, 'Coconut', 'White Domestic', 'White', 1, 'female', 'Pure white kitten. Blue eyes. Very curious and active.', '982000123456822', 'Snow white coat, blue eyes, small pink nose'),
(5, 'Pepper', 'Black and white', 'Black and white', 4, 'male', 'Black with white mustache. Very smart and trainable.', '982000123456823', 'Black with white facial marking, green eyes');

-- Insert Lost Reports (15 lost cats across Bangkok)
INSERT INTO lost_reports (cat_id, user_id, lost_date, lost_location, latitude, longitude, additional_info, reward, contact_phone, contact_email, status) VALUES
(1, 2, '2025-01-18 14:30:00', 'Near Terminal 21 Shopping Mall, Sukhumvit Road', 13.7377, 100.5617, 'Luna was last seen near Terminal 21. She is very friendly and may approach people. Wearing a pink collar with bell. Please check under parked cars.', 3000.00, '08-1234-5678', 'somchai.p@email.com', 'lost'),

(3, 3, '2025-01-17 18:00:00', 'Lumpini Park, near the lake', 13.7307, 100.5418, 'Simba loves climbing trees. Last seen chasing squirrels near the big lake in Lumpini Park. Orange with white paws. Very friendly.', 2000.00, '08-3456-7890', 'praphan.s@email.com', 'lost'),

(5, 4, '2025-01-19 07:45:00', 'Emporium Shopping Complex parking area', 13.7263, 100.5853, 'Tiger is an indoor cat who escaped through a window. Not familiar with outdoor areas. May be scared and hiding. Please check parking lots and under cars.', 5000.00, '08-4567-8901', 'araya.k@email.com', 'lost'),

(7, 5, '2025-01-16 20:00:00', 'Siam Paragon area, near Siam BTS Station', 13.7465, 100.5348, 'Daisy has folded ears and may look unusual. She is very friendly but might be scared. Last seen near Siam BTS station. Gray color.', 2500.00, '08-5678-9012', 'thanawat.r@email.com', 'lost'),

(9, 6, '2025-01-18 16:30:00', 'Central World shopping area', 13.7469, 100.5397, 'Bella is an indoor cat who escaped during a delivery. Silver-blue coat, green eyes. Very shy with strangers. May be hiding in quiet areas.', 4000.00, '08-6789-0123', 'kulap.m@email.com', 'lost'),

(11, 7, '2025-01-15 12:00:00', 'Thonglor Soi 10-12 area', 13.7338, 100.5734, 'Milo is an outdoor cat who hasn\'t returned home for 4 days. Orange ginger cat, very friendly. Usually comes home for meals. May be trapped somewhere.', 1500.00, '08-7890-1234', 'prasert.c@email.com', 'lost'),

(13, 8, '2025-01-19 09:15:00', 'Icon Siam riverside area', 13.7268, 100.5108, 'Oliver is a large, fluffy Ragdoll with blue eyes. Very docile and may not run away from people. Last seen near the riverside walkway.', 3500.00, '08-8901-2345', 'siriporn.t@email.com', 'lost'),

(14, 9, '2025-01-17 15:45:00', 'Chatuchak Weekend Market area', 13.8003, 100.5497, 'Lucy has unique tortoiseshell coloring. May be scared of the crowd. Last seen near the pet section. Please check market stalls and under tables.', 2000.00, '08-9012-3456', 'wanchai.n@email.com', 'lost'),

(17, 11, '2025-01-18 19:30:00', 'Asok BTS Station area', 13.7373, 100.5603, 'Leo is very active and athletic. Reddish-brown ticked coat. May have climbed high places. Check rooftops and balconies near Asok station.', 2500.00, '09-1234-5678', 'kittipong.l@email.com', 'lost'),

(19, 12, '2025-01-16 21:00:00', 'RCA (Royal City Avenue) entertainment district', 13.7353, 100.5653, 'Jasper is a large fluffy Norwegian Forest cat. Very friendly with people. May be confused by the noise. Brown tabby with very long fur.', 4000.00, '09-2345-6789', 'pawina.s@email.com', 'lost'),

(21, 14, '2025-01-19 10:00:00', 'Benjakitti Park area', 13.7308, 100.5607, 'Rocky has a flat face and is not good at outdoor survival. Orange exotic shorthair. Very lazy, may not travel far. Please check bushes near the park.', 3000.00, '09-4567-8901', 'ratana.w@email.com', 'lost'),

(24, 16, '2025-01-15 17:30:00', 'Ari neighborhood, near BTS Ari', 13.7788, 100.5413, 'Luna is a young kitten, only 1 year old. Gray tabby with white paws. Very playful but small and vulnerable. May be scared and hiding.', 2000.00, '09-6789-0123', 'atchara.m@email.com', 'lost'),

(27, 18, '2025-01-18 13:00:00', 'On Nut BTS Station area', 13.7053, 100.6013, 'Buddy is very social and friendly. May have followed someone or gotten on a vehicle. Orange and white. Check nearby shops and restaurants.', 1500.00, '09-8901-2345', 'suda.t@email.com', 'lost'),

(30, 20, '2025-01-17 08:30:00', 'Rama 9 MRT Station area', 13.7594, 100.5667, 'Whiskers is an old senior cat with long whiskers. Gray and white. May be confused. Not good at finding way home. Please help!', 3500.00, '09-0123-4567', 'apinya.w@email.com', 'lost'),

(33, 3, '2025-01-19 11:00:00', 'Sathorn Road near BTS Chong Nonsi', 13.7194, 100.5267, 'Coconut is a young white kitten with blue eyes. Very small and vulnerable. Escaped from apartment balcony. May be on nearby balconies or rooftops.', 2500.00, '08-3456-7890', 'praphan.s@email.com', 'lost');

-- Insert some sightings for active reports
INSERT INTO sightings (report_id, reporter_name, reporter_phone, reporter_email, sighting_date, latitude, longitude, description) VALUES
(1, 'Preecha Chan', '08-1111-2222', 'preecha@email.com', '2025-01-18 16:00:00', 13.7382, 100.5622, 'I saw a cream-colored cat with a pink collar near the BTS station exit. It was meowing and seemed lost.'),
(1, 'Siri Thana', '08-2222-3333', NULL, '2025-01-18 18:30:00', 13.7375, 100.5615, 'Spotted a Siamese cat under a parked car in Terminal 21 parking lot B2. It ran away when I approached.'),

(3, 'Nattapong M', '08-3333-4444', 'nattapong@email.com', '2025-01-17 19:30:00', 13.7315, 100.5425, 'Orange tabby cat playing near the exercise area. Very friendly, tried to follow me home.'),

(5, 'Kanokwan P', '08-4444-5555', 'kanokwan@email.com', '2025-01-19 09:00:00', 13.7268, 100.5858, 'Bengal cat spotted on 3rd floor parking, looked scared and was hiding between cars.'),

(7, 'Somjit K', '08-5555-6666', NULL, '2025-01-17 08:00:00', 13.7470, 100.5352, 'Gray cat with folded ears near Central World. Looked confused and was meowing.'),

(11, 'Anong W', '08-6666-7777', 'anong@email.com', '2025-01-16 08:00:00', 13.7342, 100.5740, 'Ginger cat seen in Soi 12. Was drinking water from a puddle. Looked hungry.'),

(13, 'Yingluck S', '08-7777-8888', NULL, '2025-01-19 14:00:00', 13.7275, 100.5115, 'Large fluffy cat with blue eyes near Icon Siam riverside walkway. Very calm, let me pet it.');

-- Show summary
SELECT 
    (SELECT COUNT(*) FROM users WHERE id > 1) as total_users,
    (SELECT COUNT(*) FROM cats) as total_cats,
    (SELECT COUNT(*) FROM lost_reports WHERE status = 'lost') as lost_cats,
    (SELECT COUNT(*) FROM sightings) as total_sightings;

-- Show Bangkok location distribution
SELECT 
    SUBSTRING_INDEX(lost_location, ',', 1) as area,
    COUNT(*) as count
FROM lost_reports
GROUP BY area
ORDER BY count DESC;
