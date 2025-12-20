<?php<?php
require_once 'includes/config.php';

// Get all lost cats
$allLostCats = getRecentLostCats(500);

// Get user location if logged in
$userLat = 40.7128;
$userLon = -74.0060;

if (isLoggedIn()) {
    $userId = getUserId();
    $userQuery = "SELECT latitude, longitude FROM users WHERE id = ?";
    $stmt = $conn->prepare($userQuery);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($userData = $result->fetch_assoc()) {
        if ($userData['latitude'] && $userData['longitude']) {
            $userLat = $userData['latitude'];
            $userLon = $userData['longitude'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lost Cats Map - PawFinder</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>
    <style>
        #fullscreen-map {
            height: calc(100vh - 80px);
            width: 100%;
        }
        
        .map-controls {
            position: absolute;
            top: 100px;
            right: 20px;
            z-index: 1000;
            background: white;
            padding: 1.5rem;
            border-radius: 15px;
            box-shadow: 0 4px 20px var(--shadow);
            max-width: 300px;
        }
        
        .map-stats {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        
        .map-stat {
            text-align: center;
            flex: 1;
        }
        
        .map-stat-number {
            font-size: 2rem;
            font-weight: 800;
            color: var(--primary);
        }
        
        .map-stat-label {
            font-size: 0.85rem;
            color: var(--text-light);
        }
        
        .filter-group {
            margin-bottom: 1rem;
        }
        
        .filter-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--text-dark);
        }
        
        .filter-group input {
            width: 100%;
            padding: 0.6rem;
            border: 2px solid #E9ECEF;
            border-radius: 8px;
            font-family: 'Manrope', sans-serif;
        }
    </style>
</head>
<body>
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

    <div style="position: relative;">
        <div id="fullscreen-map"></div>
        
        <div class="map-controls">
            <h3 style="margin-bottom: 1rem;">Map Overview</h3>
            
            <div class="map-stats">
                <div class="map-stat">
                    <div class="map-stat-number"><?php echo count($allLostCats); ?></div>
                    <div class="map-stat-label">Lost Cats</div>
                </div>
            </div>
            
            <div style="padding: 1rem; background: var(--bg-cream); border-radius: 10px; margin-bottom: 1rem;">
                <p style="font-size: 0.9rem; color: var(--text-dark); line-height: 1.6; margin: 0;">
                    🗺️ Click on any marker to see cat details. Clusters show multiple cats in the same area.
                </p>
            </div>
            
            <div style="display: flex; gap: 0.5rem;">
                <a href="report-lost.php" class="btn btn-primary" style="flex: 1; justify-content: center; font-size: 0.9rem; padding: 0.7rem;">
                    Report Lost
                </a>
            </div>
        </div>
    </div>

    <script>
        // Initialize map
        const map = L.map('fullscreen-map').setView([<?php echo $userLat; ?>, <?php echo $userLon; ?>], 11);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);

        // Create marker cluster group
        const markers = L.markerClusterGroup({
            maxClusterRadius: 60,
            spiderfyOnMaxZoom: true,
            showCoverageOnHover: false,
            zoomToBoundsOnClick: true
        });

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
        const lostCats = <?php echo json_encode($allLostCats); ?>;
        
        lostCats.forEach(cat => {
            const marker = L.marker([cat.latitude, cat.longitude], { icon: catIcon });
            
            let popupContent = `
                <div style="min-width: 250px;">
                    ${cat.photo ? `<img src="uploads/cats/${cat.photo}" class="map-popup-image" alt="${cat.cat_name}">` : ''}
                    ${cat.photo ? `
                        <img 
                            src="uploads/cats/${cat.photo}" 
                            class="map-popup-image clickable-image"
                            alt="${cat.cat_name}"
                            onclick="openImageModal(this.src)"
                        >
                    ` : ''}
                    <p class="map-popup-info"><strong>Breed:</strong> ${cat.breed || 'Mixed'}</p>
                    <p class="map-popup-info"><strong>Color:</strong> ${cat.color}</p>
                    <p class="map-popup-info"><strong>Lost:</strong> ${new Date(cat.lost_date).toLocaleDateString()}</p>
                    <p class="map-popup-info"><strong>Location:</strong> ${cat.lost_location}</p>
                    ${cat.reward > 0 ? `<p class="map-popup-info"><strong>Reward:</strong> $${cat.reward}</p>` : ''}
                    <a href="cat-details.php?id=${cat.id}" class="map-popup-btn">View Details</a>
                </div>
            `;
            
            marker.bindPopup(popupContent);
            markers.addLayer(marker);
        });

        map.addLayer(markers);

        <?php if (isLoggedIn()): ?>
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
</script>
</script>
</body>
</html>

</body>
</html>
