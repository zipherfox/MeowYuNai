<?php
require_once 'includes/config.php';
requireLogin();

$userId = getUserId();
$error = '';
$success = '';

// Get user's cats
$catsQuery = "SELECT * FROM cats WHERE user_id = ? ORDER BY name";
$stmt = $conn->prepare($catsQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$cats = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Pre-select cat if provided
$selectedCatId = $_GET['cat_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $catId = intval($_POST['cat_id']);
    $lostDate = sanitize($_POST['lost_date']);
    $lostLocation = sanitize($_POST['lost_location']);
    $latitude = floatval($_POST['latitude']);
    $longitude = floatval($_POST['longitude']);
    $additionalInfo = sanitize($_POST['additional_info']);
    $reward = floatval($_POST['reward']);
    $contactPhone = sanitize($_POST['contact_phone']);
    $contactEmail = sanitize($_POST['contact_email']);
    
    // Verify cat belongs to user
    $verifySql = "SELECT id FROM cats WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($verifySql);
    $stmt->bind_param("ii", $catId, $userId);
    $stmt->execute();
    
    if ($stmt->get_result()->num_rows > 0) {
        // Check if cat is already reported lost
        $checkSql = "SELECT id FROM lost_reports WHERE cat_id = ? AND status = 'lost'";
        $stmt = $conn->prepare($checkSql);
        $stmt->bind_param("i", $catId);
        $stmt->execute();
        
        if ($stmt->get_result()->num_rows > 0) {
            $error = 'This cat is already reported as lost.';
        } else {
            $insertSql = "INSERT INTO lost_reports (cat_id, user_id, lost_date, lost_location, latitude, longitude, additional_info, reward, contact_phone, contact_email) 
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insertSql);
            $stmt->bind_param("iissddsss", $catId, $userId, $lostDate, $lostLocation, $latitude, $longitude, $additionalInfo, $reward, $contactPhone, $contactEmail);
            
            if ($stmt->execute()) {
                $success = 'Lost report submitted successfully! Your cat is now visible on the map.';
                $reportId = $stmt->insert_id;
                header("Location: cat-details.php?id=$reportId");
                exit;
            } else {
                $error = 'Failed to submit report. Please try again.';
            }
        }
    } else {
        $error = 'Invalid cat selected.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Lost Cat - PawFinder</title>
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
                <li><a href="dashboard.php" class="nav-link">Dashboard</a></li>
                <li><a href="my-cats.php" class="nav-link">My Cats</a></li>
                <li><a href="report-lost.php" class="nav-link">Report Lost</a></li>
                <li><a href="logout.php" class="btn btn-outline">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="form-container">
        <h2>🚨 Report Lost Cat</h2>
        <p style="color: var(--text-light); margin-bottom: 2rem;">
            Fill in the details below to alert the community about your lost cat
        </p>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <?php if (empty($cats)): ?>
            <div class="alert alert-info">
                You need to register a cat first before reporting it as lost. 
                <a href="my-cats.php?action=add" style="color: var(--primary); font-weight: 600;">Add a cat now</a>
            </div>
        <?php else: ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="cat_id">Select Cat *</label>
                <select id="cat_id" name="cat_id" class="form-control" required>
                    <option value="">-- Select a cat --</option>
                    <?php foreach ($cats as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>" 
                            <?php echo ($selectedCatId == $cat['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['name']); ?> 
                        (<?php echo htmlspecialchars($cat['color']); ?>, 
                         <?php echo htmlspecialchars($cat['breed'] ?: 'Mixed'); ?>)
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="lost_date">When was your cat last seen? *</label>
                <input type="datetime-local" id="lost_date" name="lost_date" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="lost_location">Location Description *</label>
                <input type="text" id="lost_location" name="lost_location" class="form-control" required
                       placeholder="e.g., Near Central Park, 123 Main Street">
            </div>

            <div class="form-group">
                <label>Mark Location on Map *</label>
                <div id="map" style="height: 400px; border-radius: 10px; margin-bottom: 1rem;"></div>
                <p style="color: var(--text-light); font-size: 0.9rem;">
                    Click on the map to mark where your cat was last seen
                </p>
            </div>

            <input type="hidden" id="latitude" name="latitude" required>
            <input type="hidden" id="longitude" name="longitude" required>

            <div class="form-group">
                <label for="additional_info">Additional Information</label>
                <textarea id="additional_info" name="additional_info" class="form-control" 
                          placeholder="Any additional details that might help find your cat (behavior, favorite spots, medical conditions, etc.)"></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="reward">Reward Amount ($)</label>
                    <input type="number" id="reward" name="reward" class="form-control" 
                           min="0" step="0.01" value="0">
                </div>

                <div class="form-group">
                    <label for="contact_phone">Contact Phone *</label>
                    <input type="tel" id="contact_phone" name="contact_phone" class="form-control" required>
                </div>
            </div>

            <div class="form-group">
                <label for="contact_email">Contact Email</label>
                <input type="email" id="contact_email" name="contact_email" class="form-control">
            </div>

            <div style="background: var(--bg-cream); padding: 1.5rem; border-radius: 10px; margin-bottom: 1.5rem;">
                <h4 style="margin-bottom: 0.5rem;">⚠️ Important Tips</h4>
                <ul style="margin-left: 1.5rem; color: var(--text-light); line-height: 1.8;">
                    <li>Report your lost cat as soon as possible</li>
                    <li>Check nearby shelters and veterinary clinics</li>
                    <li>Leave familiar items outside (litter box, bedding)</li>
                    <li>Search at dawn and dusk when cats are most active</li>
                    <li>Alert your neighbors and local community</li>
                </ul>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">
                🚨 Submit Lost Report
            </button>
        </form>

        <?php endif; ?>
    </div>

    <footer class="footer">
        <div class="container">
            <div class="footer-bottom">
                <p>&copy; 2025 PawFinder. All rights reserved. Made with ❤️ for cats.</p>
            </div>
        </div>
    </footer>

    <script>
        // Set default date to now
        document.getElementById('lost_date').value = new Date().toISOString().slice(0, 16);

        // Initialize map
        const map = L.map('map').setView([40.7128, -74.0060], 12);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        let marker = null;

        // Try to get user's current location
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                
                map.setView([lat, lng], 14);
            });
        }

        // Add marker on map click
        map.on('click', function(e) {
            const lat = e.latlng.lat;
            const lng = e.latlng.lng;
            
            if (marker) {
                marker.setLatLng([lat, lng]);
            } else {
                marker = L.marker([lat, lng], { draggable: true }).addTo(map);
                marker.on('dragend', updateCoordinates);
            }
            
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;
        });

        function updateCoordinates(e) {
            const position = e.target.getLatLng();
            document.getElementById('latitude').value = position.lat;
            document.getElementById('longitude').value = position.lng;
        }

        // Geocoding for location input
        document.getElementById('lost_location').addEventListener('blur', function() {
            const address = this.value;
            if (address) {
                fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.length > 0) {
                            const lat = parseFloat(data[0].lat);
                            const lon = parseFloat(data[0].lon);
                            
                            map.setView([lat, lon], 15);
                            
                            if (marker) {
                                marker.setLatLng([lat, lon]);
                            } else {
                                marker = L.marker([lat, lon], { draggable: true }).addTo(map);
                                marker.on('dragend', updateCoordinates);
                            }
                            
                            document.getElementById('latitude').value = lat;
                            document.getElementById('longitude').value = lon;
                        }
                    });
            }
        });
    </script>
</body>
</html>
