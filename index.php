<?php
require_once 'includes/config.php';

// Get user location if logged in
$userLat = null;
$userLon = null;
$nearbyLostCats = [];

if (isLoggedIn()) {
    $userId = getUserId();
    $userQuery = "SELECT latitude, longitude FROM users WHERE id = ?";
    $stmt = $conn->prepare($userQuery);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($userData = $result->fetch_assoc()) {
        $userLat = $userData['latitude'];
        $userLon = $userData['longitude'];
        
        if ($userLat && $userLon) {
            $nearbyLostCats = getLostCatsNearLocation($userLat, $userLon, 50);
        }
    }
}

// Get all lost cats for map
$allLostCats = getRecentLostCats(100);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lost Cat Tracker - Find Your Furry Friend</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</head>
<body>
    <script>
    window.openImageModal = function (src) {
        const modal = document.getElementById('imageModal');
        const modalImg = document.getElementById('modalImage');

        modalImg.src = src;
        modal.classList.add('active');
    };

    window.closeImageModal = function (event) {
        if (event) event.stopPropagation();
        document.getElementById('imageModal').classList.remove('active');
    };
    </script>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="nav-logo">PawFinder</a>
            <ul class="nav-menu">
                <li><a href="index.php" class="nav-link">Home</a></li>
                <li><a href="map.php" class="nav-link">Map</a></li>
                <?php if (isLoggedIn()): ?>
                    <li><a href="dashboard.php" class="nav-link">Dashboard</a></li>
                    <li><a href="my-cats.php" class="nav-link">My Cats</a></li>
                    <li><a href="report-lost.php" class="nav-link">Report Lost</a></li>
                    <li><a href="logout.php" class="btn btn-outline">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php" class="nav-link">Login</a></li>
                    <li><a href="register.php" class="btn btn-primary">Sign Up</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <div class="hero-text">
                    <h1>Find Your Lost Cat with Community Help</h1>
                    <p>Join thousands of pet owners using our real-time mapping system to reunite with their beloved cats. Report, track, and find lost cats in your area.</p>
                    <div class="hero-buttons">
                        <?php if (isLoggedIn()): ?>
                            <a href="report-lost.php" class="btn btn-primary">Report Lost Cat</a>
                            <a href="map.php" class="btn btn-secondary">View Map</a>
                        <?php else: ?>
                            <a href="register.php" class="btn btn-primary">Get Started Free</a>
                            <a href="map.php" class="btn btn-secondary">View Lost Cats</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="hero-image">
                    <svg width="500" height="400" viewBox="0 0 500 400" style="filter: drop-shadow(0 20px 40px rgba(255, 107, 53, 0.2));">
                        <!-- Cat illustration -->
                        <ellipse cx="250" cy="300" rx="120" ry="80" fill="#FF6B35" opacity="0.2"/>
                        <ellipse cx="250" cy="200" rx="100" ry="110" fill="#FF6B35"/>
                        <ellipse cx="220" cy="150" rx="30" ry="40" fill="#FF6B35"/>
                        <ellipse cx="280" cy="150" rx="30" ry="40" fill="#FF6B35"/>
                        <circle cx="230" cy="190" r="12" fill="#2C3E50"/>
                        <circle cx="270" cy="190" r="12" fill="#2C3E50"/>
                        <circle cx="232" cy="192" r="6" fill="white"/>
                        <circle cx="272" cy="192" r="6" fill="white"/>
                        <path d="M 240 210 Q 250 220 260 210" stroke="#2C3E50" stroke-width="3" fill="none"/>
                        <line x1="200" y1="200" x2="150" y2="190" stroke="#2C3E50" stroke-width="2"/>
                        <line x1="300" y1="200" x2="350" y2="190" stroke="#2C3E50" stroke-width="2"/>
                        <line x1="200" y1="210" x2="150" y2="210" stroke="#2C3E50" stroke-width="2"/>
                        <line x1="300" y1="210" x2="350" y2="210" stroke="#2C3E50" stroke-width="2"/>
                        <!-- Location pin -->
                        <g transform="translate(350, 100)">
                            <path d="M 0 0 C -15 -20 -15 -35 0 -50 C 15 -35 15 -20 0 0 Z" fill="#4ECDC4"/>
                            <circle cx="0" cy="-30" r="10" fill="white"/>
                        </g>
                    </svg>
                </div>
            </div>
        </div>
    </section>

    <!-- Nearby Lost Cats Section -->
    <?php if (isLoggedIn() && !empty($nearbyLostCats)): ?>
    <section class="map-section" style="background: var(--bg-cream);">
        <div class="container">
            <div class="section-header">
                <h2>Lost Cats Near You</h2>
                <p>These cats were recently reported lost within 50km of your location</p>
            </div>
            <div class="cards-grid">
                <?php foreach (array_slice($nearbyLostCats, 0, 6) as $cat): ?>
                <div class="cat-card">
                    <div style="position: relative;">
                        <?php if ($cat['photo']): ?>
                            <img src="uploads/cats/<?php echo htmlspecialchars($cat['photo']); ?>" 
                                alt="<?php echo htmlspecialchars($cat['cat_name']); ?>" 
                                class="cat-card-image clickable-image"
                                onclick="openImageModal(this.src)">
                        <?php else: ?>
                            <div class="cat-card-image" style="background: linear-gradient(135deg, #FFE66D 0%, #FF6B35 100%); display: flex; align-items: center; justify-content: center; font-size: 4rem;">
                                🐱
                            </div>
                        <?php endif; ?>
                        <div class="cat-card-badge">LOST</div>
                    </div>
                    <div class="cat-card-content">
                        <h3 class="cat-card-title"><?php echo htmlspecialchars($cat['cat_name']); ?></h3>
                        <div class="cat-card-info">
                            <span><strong>Breed:</strong> <?php echo htmlspecialchars($cat['breed'] ?: 'Mixed'); ?></span>
                            <span><strong>Color:</strong> <?php echo htmlspecialchars($cat['color']); ?></span>
                            <span><strong>Age:</strong> <?php echo htmlspecialchars($cat['age']); ?> years</span>
                        </div>
                        <div class="cat-card-location">
                            📍 <?php echo $cat['distance']; ?> km away
                        </div>
                        <p class="cat-card-description">
                            <?php echo htmlspecialchars(substr($cat['additional_info'], 0, 100)); ?>...
                        </p>
                        <a href="cat-details.php?id=<?php echo $cat['id']; ?>" class="btn btn-primary" style="width: 100%; justify-content: center;">
                            View Details
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Map Section -->
    <section class="map-section">
        <div class="container">
            <div class="section-header">
                <h2>Live Lost Cat Map</h2>
                <p>Real-time locations of reported lost cats in your area</p>
            </div>
            <div id="map"></div>
        </div>
    </section>

    <!-- Recent Lost Cats -->
    <section class="map-section" style="background: var(--bg-cream);">
        <div class="container">
            <div class="section-header">
                <h2>Recently Reported</h2>
                <p>Latest lost cat reports from our community</p>
            </div>
            <div class="cards-grid">
                <?php foreach (array_slice($allLostCats, 0, 6) as $cat): ?>
                <div class="cat-card">
                    <div style="position: relative;">
                        <?php if ($cat['photo']): ?>
                           <img src="uploads/cats/<?php echo htmlspecialchars($cat['photo']); ?>" 
                                alt="<?php echo htmlspecialchars($cat['cat_name']); ?>" 
                                class="cat-card-image clickable-image"
                                onclick="openImageModal(this.src)">
                        <?php else: ?>
                            <div class="cat-card-image" style="background: linear-gradient(135deg, #4ECDC4 0%, #FF6B35 100%); display: flex; align-items: center; justify-content: center; font-size: 4rem;">
                                🐱
                            </div>
                        <?php endif; ?>
                        <div class="cat-card-badge">LOST</div>
                    </div>
                    <div class="cat-card-content">
                        <h3 class="cat-card-title"><?php echo htmlspecialchars($cat['cat_name']); ?></h3>
                        <div class="cat-card-info">
                            <span><strong>Breed:</strong> <?php echo htmlspecialchars($cat['breed'] ?: 'Mixed'); ?></span>
                            <span><strong>Lost:</strong> <?php echo formatDate($cat['lost_date']); ?></span>
                        </div>
                        <div class="cat-card-location">
                            📍 <?php echo htmlspecialchars($cat['lost_location']); ?>
                        </div>
                        <a href="cat-details.php?id=<?php echo $cat['id']; ?>" class="btn btn-primary" style="width: 100%; justify-content: center;">
                            View Details
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>PawFinder</h3>
                    <p>Helping reunite lost cats with their families through community support and real-time tracking.</p>
                </div>
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <ul class="footer-links">
                        <li><a href="index.php">Home</a></li>
                        <li><a href="map.php">Map</a></li>
                        <li><a href="about.php">About Us</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Support</h3>
                    <ul class="footer-links">
                        <li><a href="help.php">Help Center</a></li>
                        <li><a href="contact.php">Contact</a></li>
                        <li><a href="privacy.php">Privacy</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Community</h3>
                    <ul class="footer-links">
                        <li><a href="tips.php">Lost Cat Tips</a></li>
                        <li><a href="success.php">Success Stories</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 PawFinder. All rights reserved. Made with ❤️ for cats.</p>
            </div>
        </div>
    </footer>

    <script>
        // Initialize map
        const map = L.map('map').setView([<?php echo $userLat ?: '40.7128'; ?>, <?php echo $userLon ?: '-74.0060'; ?>], 12);
        
        // Add tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);

        // Custom cat icon (without emoji to avoid btoa error)
        const catIcon = L.icon({
            iconUrl: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(`
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40">
                    <circle cx="20" cy="20" r="18" fill="#FF6B35" stroke="white" stroke-width="2"/>
                    <g transform="translate(20,20)">
                        <!-- Cat ears -->
                        <path d="M -8,-10 L -6,-4 L -10,-6 Z" fill="white"/>
                        <path d="M 8,-10 L 6,-4 L 10,-6 Z" fill="white"/>
                        <!-- Cat face -->
                        <circle cx="0" cy="0" r="8" fill="white"/>
                        <!-- Cat eyes -->
                        <circle cx="-3" cy="-1" r="1.5" fill="#2C3E50"/>
                        <circle cx="3" cy="-1" r="1.5" fill="#2C3E50"/>
                        <!-- Cat nose -->
                        <circle cx="0" cy="2" r="1" fill="#FF6B35"/>
                        <!-- Cat mouth -->
                        <path d="M -2,3 Q 0,4 2,3" stroke="#2C3E50" stroke-width="0.5" fill="none"/>
                    </g>
                </svg>
            `),
            iconSize: [40, 40],
            iconAnchor: [20, 40],
            popupAnchor: [0, -40]
        });

        // Add markers for lost cats
        <?php foreach ($allLostCats as $cat): ?>
        L.marker([<?php echo $cat['latitude']; ?>, <?php echo $cat['longitude']; ?>], { icon: catIcon })
            .bindPopup(`
                <div style="min-width: 250px;">
                    <?php if ($cat['photo']): ?>
                        <img src="uploads/cats/<?php echo htmlspecialchars($cat['photo']); ?>" 
                            class="map-popup-image clickable-image"
                            alt="<?php echo htmlspecialchars($cat['cat_name']); ?>"
                            onclick="openImageModal(this.src)">
                    <?php else: ?>
                        <div class="map-popup-image" style="background:#eee;display:flex;align-items:center;justify-content:center;font-size:3rem;">
                            🐱
                        </div>
                    <?php endif; ?>

                    <h3 class="map-popup-title"><?php echo htmlspecialchars($cat['cat_name']); ?></h3>

                    <p class="map-popup-info"><strong>Breed:</strong> <?php echo htmlspecialchars($cat['breed'] ?: 'Mixed'); ?></p>
                    <p class="map-popup-info"><strong>Color:</strong> <?php echo htmlspecialchars($cat['color']); ?></p>
                    <p class="map-popup-info"><strong>Lost:</strong> <?php echo formatDate($cat['lost_date']); ?></p>
                    <p class="map-popup-info"><strong>Location:</strong> <?php echo htmlspecialchars($cat['lost_location']); ?></p>
                    <a href="cat-details.php?id=<?php echo $cat['id']; ?>" class="map-popup-btn">View Details</a>
                </div>
            `)
            .addTo(map);
        <?php endforeach; ?>

        <?php if ($userLat && $userLon): ?>
        // Add user location marker
        const userIcon = L.icon({
            iconUrl: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(`
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40">
                    <circle cx="20" cy="20" r="18" fill="#4ECDC4" stroke="white" stroke-width="2"/>
                    <g transform="translate(20,12)">
                        <!-- Location pin -->
                        <path d="M 0,0 C -4,-6 -4,-10 0,-14 C 4,-10 4,-6 0,0 Z" fill="white"/>
                        <circle cx="0" cy="-8" r="3" fill="#4ECDC4"/>
                    </g>
                </svg>
            `),
            iconSize: [40, 40],
            iconAnchor: [20, 40]
        });

        L.marker([<?php echo $userLat; ?>, <?php echo $userLon; ?>], { icon: userIcon })
            .bindPopup('<strong>Your Location</strong>')
            .addTo(map);

        // Add circle to show 50km radius
        L.circle([<?php echo $userLat; ?>, <?php echo $userLon; ?>], {
            color: '#4ECDC4',
            fillColor: '#4ECDC4',
            fillOpacity: 0.1,
            radius: 50000
        }).addTo(map);
        <?php endif; ?>
    </script>
<!-- Image Modal -->
<div id="imageModal" class="modal" onclick="closeImageModal()">
    <span class="modal-close" onclick="closeImageModal()">&times;</span>
    <img id="modalImage" 
     style="max-width:90%; max-height:90%; border-radius:20px;"
     onclick="event.stopPropagation();">

</div>
<script>
function openImageModal(src) {
    document.getElementById('modalImage').src = src;
    document.getElementById('imageModal').classList.add('active');
}

function closeImageModal() {
    document.getElementById('imageModal').classList.remove('active');
}
</body>
</html>
