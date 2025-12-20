<?php
require_once 'includes/config.php';

$reportId = $_GET['id'] ?? null;

if (!$reportId) {
    header('Location: index.php');
    exit;
}

// Get report details
$sql = "SELECT lr.*, c.name as cat_name, c.breed, c.color, c.age, c.gender, c.photo, 
               c.description as cat_description, c.microchip_id, c.distinctive_features,
               u.full_name as owner_name, u.phone as owner_phone, u.email as owner_email
        FROM lost_reports lr
        JOIN cats c ON lr.cat_id = c.id
        JOIN users u ON lr.user_id = u.id
        WHERE lr.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $reportId);
$stmt->execute();
$report = $stmt->get_result()->fetch_assoc();

if (!$report) {
    header('Location: index.php');
    exit;
}

// Get sightings
$sightingsSql = "SELECT * FROM sightings WHERE report_id = ? ORDER BY sighting_date DESC";
$stmt = $conn->prepare($sightingsSql);
$stmt->bind_param("i", $reportId);
$stmt->execute();
$sightings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$isOwner = isLoggedIn() && getUserId() == $report['user_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($report['cat_name']); ?> - Lost Cat - PawFinder</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
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

    <section class="dashboard">
        <div class="container">
            <?php if ($report['status'] === 'found'): ?>
            <div class="alert alert-success" style="font-size: 1.2rem; text-align: center;">
                🎉 Great news! <?php echo htmlspecialchars($report['cat_name']); ?> has been found and reunited!
            </div>
            <?php endif; ?>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
                <!-- Left Column - Cat Image and Basic Info -->
                <div>
                    <div style="background: var(--bg-white); padding: 2rem; border-radius: 20px; box-shadow: 0 4px 20px var(--shadow); margin-bottom: 2rem;">
                        <?php if ($report['photo']): ?>
                            <img src="uploads/cats/<?php echo htmlspecialchars($report['photo']); ?>" 
                                 alt="<?php echo htmlspecialchars($report['cat_name']); ?>"
                                 style="width: 100%; border-radius: 15px; margin-bottom: 1.5rem;">
                        <?php else: ?>
                            <div style="width: 100%; height: 400px; background: linear-gradient(135deg, #FFE66D 0%, #FF6B35 100%); border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 8rem; margin-bottom: 1.5rem;">
                                🐱
                            </div>
                        <?php endif; ?>

                        <div style="text-align: center;">
                            <?php if ($report['status'] === 'lost'): ?>
                                <div style="background: var(--danger); color: white; display: inline-block; padding: 0.8rem 2rem; border-radius: 30px; font-weight: 700; font-size: 1.1rem; margin-bottom: 1rem;">
                                    🚨 LOST CAT
                                </div>
                            <?php else: ?>
                                <div style="background: var(--success); color: white; display: inline-block; padding: 0.8rem 2rem; border-radius: 30px; font-weight: 700; font-size: 1.1rem; margin-bottom: 1rem;">
                                    ✅ FOUND
                                </div>
                            <?php endif; ?>

                            <h1><?php echo htmlspecialchars($report['cat_name']); ?></h1>
                            
                            <?php if ($report['reward'] > 0): ?>
                            <div style="background: var(--accent); color: var(--text-dark); padding: 1rem; border-radius: 15px; margin-top: 1rem; font-weight: 700; font-size: 1.2rem;">
                                💰 Reward: $<?php echo number_format($report['reward'], 2); ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Cat Information -->
                    <div style="background: var(--bg-white); padding: 2rem; border-radius: 20px; box-shadow: 0 4px 20px var(--shadow);">
                        <h3>Cat Information</h3>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 1rem;">
                            <div>
                                <strong style="color: var(--text-light);">Breed</strong>
                                <p style="margin-top: 0.3rem;"><?php echo htmlspecialchars($report['breed'] ?: 'Mixed'); ?></p>
                            </div>
                            <div>
                                <strong style="color: var(--text-light);">Color</strong>
                                <p style="margin-top: 0.3rem;"><?php echo htmlspecialchars($report['color']); ?></p>
                            </div>
                            <div>
                                <strong style="color: var(--text-light);">Age</strong>
                                <p style="margin-top: 0.3rem;"><?php echo htmlspecialchars($report['age']); ?> years</p>
                            </div>
                            <div>
                                <strong style="color: var(--text-light);">Gender</strong>
                                <p style="margin-top: 0.3rem;"><?php echo ucfirst($report['gender']); ?></p>
                            </div>
                        </div>

                        <?php if ($report['microchip_id']): ?>
                        <div style="margin-top: 1.5rem; padding: 1rem; background: var(--bg-cream); border-radius: 10px;">
                            <strong>Microchip ID:</strong> <?php echo htmlspecialchars($report['microchip_id']); ?>
                        </div>
                        <?php endif; ?>

                        <?php if ($report['distinctive_features']): ?>
                        <div style="margin-top: 1.5rem;">
                            <strong style="color: var(--text-light);">Distinctive Features</strong>
                            <p style="margin-top: 0.5rem;"><?php echo nl2br(htmlspecialchars($report['distinctive_features'])); ?></p>
                        </div>
                        <?php endif; ?>

                        <?php if ($report['cat_description']): ?>
                        <div style="margin-top: 1.5rem;">
                            <strong style="color: var(--text-light);">Description</strong>
                            <p style="margin-top: 0.5rem;"><?php echo nl2br(htmlspecialchars($report['cat_description'])); ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Right Column - Lost Details and Map -->
                <div>
                    <!-- Lost Details -->
                    <div style="background: var(--bg-white); padding: 2rem; border-radius: 20px; box-shadow: 0 4px 20px var(--shadow); margin-bottom: 2rem;">
                        <h3>Lost Report Details</h3>
                        
                        <div style="margin-top: 1.5rem;">
                            <strong style="color: var(--text-light);">Last Seen</strong>
                            <p style="margin-top: 0.3rem; font-size: 1.1rem;">
                                📅 <?php echo formatDate($report['lost_date']); ?>
                            </p>
                        </div>

                        <div style="margin-top: 1.5rem;">
                            <strong style="color: var(--text-light);">Location</strong>
                            <p style="margin-top: 0.3rem; font-size: 1.1rem;">
                                📍 <?php echo htmlspecialchars($report['lost_location']); ?>
                            </p>
                        </div>

                        <?php if ($report['additional_info']): ?>
                        <div style="margin-top: 1.5rem;">
                            <strong style="color: var(--text-light);">Additional Information</strong>
                            <p style="margin-top: 0.5rem; line-height: 1.6;">
                                <?php echo nl2br(htmlspecialchars($report['additional_info'])); ?>
                            </p>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Map -->
                    <div style="background: var(--bg-white); padding: 2rem; border-radius: 20px; box-shadow: 0 4px 20px var(--shadow); margin-bottom: 2rem;">
                        <h3>Location Map</h3>
                        <div id="map" style="height: 350px; border-radius: 10px; margin-top: 1rem;"></div>
                    </div>

                    <!-- Contact Information -->
                    <div style="background: var(--primary); color: white; padding: 2rem; border-radius: 20px; box-shadow: 0 4px 20px var(--shadow-strong);">
                        <h3 style="color: white;">📞 Contact Information</h3>
                        <p style="margin-top: 1rem;">If you have seen this cat or have any information, please contact:</p>
                        
                        <div style="margin-top: 1.5rem; font-size: 1.1rem;">
                            <p><strong>Owner:</strong> <?php echo htmlspecialchars($report['owner_name']); ?></p>
                            <p style="margin-top: 0.5rem;"><strong>Phone:</strong> 
                                <a href="tel:<?php echo htmlspecialchars($report['contact_phone']); ?>" 
                                   style="color: white; text-decoration: underline;">
                                    <?php echo htmlspecialchars($report['contact_phone']); ?>
                                </a>
                            </p>
                            <?php if ($report['contact_email']): ?>
                            <p style="margin-top: 0.5rem;"><strong>Email:</strong> 
                                <a href="mailto:<?php echo htmlspecialchars($report['contact_email']); ?>" 
                                   style="color: white; text-decoration: underline;">
                                    <?php echo htmlspecialchars($report['contact_email']); ?>
                                </a>
                            </p>
                            <?php endif; ?>
                        </div>

                        <?php if ($isOwner && $report['status'] === 'lost'): ?>
                        <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid rgba(255,255,255,0.3);">
                            <a href="mark-found.php?id=<?php echo $reportId; ?>" 
                               class="btn" 
                               style="background: white; color: var(--primary); width: 100%; justify-content: center;"
                               onclick="return confirm('Mark this cat as found? This will remove it from the lost cats map.');">
                                ✅ Mark as Found
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Sightings Section -->
            <?php if (!empty($sightings)): ?>
            <div style="background: var(--bg-white); padding: 2rem; border-radius: 20px; box-shadow: 0 4px 20px var(--shadow);">
                <h3>Community Sightings (<?php echo count($sightings); ?>)</h3>
                <div style="margin-top: 1.5rem;">
                    <?php foreach ($sightings as $sighting): ?>
                    <div style="padding: 1.5rem; background: var(--bg-cream); border-radius: 15px; margin-bottom: 1rem;">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
                            <strong><?php echo htmlspecialchars($sighting['reporter_name'] ?: 'Anonymous'); ?></strong>
                            <span style="color: var(--text-light); font-size: 0.9rem;">
                                <?php echo formatDate($sighting['sighting_date']); ?>
                            </span>
                        </div>
                        <p style="color: var(--text-light); margin-bottom: 0.5rem;">
                            📍 Lat: <?php echo $sighting['latitude']; ?>, Lng: <?php echo $sighting['longitude']; ?>
                        </p>
                        <p><?php echo nl2br(htmlspecialchars($sighting['description'])); ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <div class="footer-bottom">
                <p>&copy; 2025 PawFinder. All rights reserved. Made with ❤️ for cats.</p>
            </div>
        </div>
    </footer>

    <script>
        // Initialize map
        const map = L.map('map').setView([<?php echo $report['latitude']; ?>, <?php echo $report['longitude']; ?>], 15);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Add marker for lost location
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
            iconAnchor: [20, 40]
        });

        L.marker([<?php echo $report['latitude']; ?>, <?php echo $report['longitude']; ?>], { icon: catIcon })
            .bindPopup('<strong>Last seen here</strong><br><?php echo htmlspecialchars($report['lost_location']); ?>')
            .addTo(map)
            .openPopup();

        // Add sighting markers
        <?php foreach ($sightings as $index => $sighting): ?>
        L.circleMarker([<?php echo $sighting['latitude']; ?>, <?php echo $sighting['longitude']; ?>], {
            radius: 8,
            fillColor: "#4ECDC4",
            color: "#fff",
            weight: 2,
            opacity: 1,
            fillOpacity: 0.8
        })
        .bindPopup(`
            <strong>Sighting <?php echo $index + 1; ?></strong><br>
            <?php echo formatDate($sighting['sighting_date']); ?><br>
            <?php echo htmlspecialchars($sighting['description']); ?>
        `)
        .addTo(map);
        <?php endforeach; ?>
    </script>
</body>
</html>