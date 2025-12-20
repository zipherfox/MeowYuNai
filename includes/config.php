<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'lost_cat_tracker');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8mb4
$conn->set_charset("utf8mb4");

// Session configuration
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Helper functions
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getUserId() {
    return $_SESSION['user_id'] ?? null;
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function redirectIfLoggedIn() {
    if (isLoggedIn()) {
        header('Location: dashboard.php');
        exit;
    }
}

function sanitize($data) {
    global $conn;
    return $conn->real_escape_string(trim($data));
}

function calculateDistance($lat1, $lon1, $lat2, $lon2) {
    // Haversine formula to calculate distance in kilometers
    $earthRadius = 6371;
    
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    
    $a = sin($dLat/2) * sin($dLat/2) +
         cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
         sin($dLon/2) * sin($dLon/2);
    
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    $distance = $earthRadius * $c;
    
    return $distance;
}

function uploadPhoto($file) {
    // Absolute path to upload folder
    $uploadDir = __DIR__ . '/../uploads/cats/';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        return null;
    }

    $newFilename = uniqid('cat_') . '_' . time() . '.' . $ext;
    $destination = $uploadDir . $newFilename;

    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return $newFilename; 
    }

    return null;
}


function formatDate($datetime) {
    return date('M d, Y h:i A', strtotime($datetime));
}

function getRecentLostCats($limit = 10) {
    global $conn;
    
    $sql = "SELECT lr.*, c.name as cat_name, c.breed, c.color, c.photo, c.age, 
                   u.full_name as owner_name, u.phone as owner_phone
            FROM lost_reports lr
            JOIN cats c ON lr.cat_id = c.id
            JOIN users u ON lr.user_id = u.id
            WHERE lr.status = 'lost'
            ORDER BY lr.created_at DESC
            LIMIT ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getLostCatsNearLocation($lat, $lon, $radius = 5) {
    global $conn;
    
    // Get all lost cats
    $sql = "SELECT lr.*, c.name as cat_name, c.breed, c.color, c.photo, c.age, c.gender,
                   u.full_name as owner_name, u.phone as owner_phone, u.email as owner_email
            FROM lost_reports lr
            JOIN cats c ON lr.cat_id = c.id
            JOIN users u ON lr.user_id = u.id
            WHERE lr.status = 'lost'";
    
    $result = $conn->query($sql);
    $cats = [];
    
    while ($row = $result->fetch_assoc()) {
        $distance = calculateDistance($lat, $lon, $row['latitude'], $row['longitude']);
        
        if ($distance <= $radius) {
            $row['distance'] = round($distance, 2);
            $cats[] = $row;
        }
    }
    
    // Sort by distance
    usort($cats, function($a, $b) {
        return $a['distance'] <=> $b['distance'];
    });
    
    return $cats;
}
?>
