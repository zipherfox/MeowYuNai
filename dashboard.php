<?php
require_once 'includes/config.php';
requireLogin();

$userId = getUserId();

// Get user info
$userQuery = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($userQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Get user's cats
$catsQuery = "SELECT * FROM cats WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($catsQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$cats = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get user's lost reports
$reportsQuery = "SELECT lr.*, c.name as cat_name, c.photo 
                 FROM lost_reports lr 
                 JOIN cats c ON lr.cat_id = c.id 
                 WHERE lr.user_id = ? 
                 ORDER BY lr.created_at DESC";
$stmt = $conn->prepare($reportsQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$reports = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get statistics
$totalCats = count($cats);
$activeLostReports = count(array_filter($reports, function($r) { return $r['status'] === 'lost'; }));
$foundCats = count(array_filter($reports, function($r) { return $r['status'] === 'found'; }));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - PawFinder</title>
    <link rel="stylesheet" href="css/style.css">
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

    <section class="dashboard">
        <div class="container">
            <div class="dashboard-header">
                <h1>Welcome back, <?php echo htmlspecialchars($user['full_name']); ?>! 👋</h1>
                <p style="color: var(--text-light); font-size: 1.2rem;">Here's an overview of your account</p>
            </div>

            <!-- Statistics -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $totalCats; ?></div>
                    <div class="stat-label">Registered Cats</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" style="color: var(--danger);"><?php echo $activeLostReports; ?></div>
                    <div class="stat-label">Currently Lost</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" style="color: var(--success);"><?php echo $foundCats; ?></div>
                    <div class="stat-label">Found & Reunited</div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div style="background: var(--bg-white); padding: 2rem; border-radius: 20px; box-shadow: 0 4px 20px var(--shadow); margin-bottom: 3rem;">
                <h2 style="margin-bottom: 1.5rem;">Quick Actions</h2>
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <a href="my-cats.php?action=add" class="btn btn-primary">➕ Add New Cat</a>
                    <a href="report-lost.php" class="btn btn-secondary">🚨 Report Lost Cat</a>
                    <a href="map.php" class="btn btn-outline">🗺️ View Map</a>
                </div>
            </div>

            <!-- My Cats -->
            <div style="background: var(--bg-white); padding: 2rem; border-radius: 20px; box-shadow: 0 4px 20px var(--shadow); margin-bottom: 3rem;">
                <h2 style="margin-bottom: 1.5rem;">My Cats</h2>
                <?php if (empty($cats)): ?>
                    <p style="color: var(--text-light); text-align: center; padding: 2rem;">
                        You haven't registered any cats yet. <a href="my-cats.php?action=add" style="color: var(--primary); font-weight: 600;">Add your first cat</a>
                    </p>
                <?php else: ?>
                    <div class="cards-grid">
                        <?php foreach ($cats as $cat): ?>
                        <div class="cat-card">
                            <div style="position: relative;">
                                <?php if ($cat['photo']): ?>
                                    <img src="uploads/cats/<?php echo htmlspecialchars($cat['photo']); ?>" 
                                         alt="<?php echo htmlspecialchars($cat['name']); ?>" 
                                         class="cat-card-image">
                                <?php else: ?>
                                    <div class="cat-card-image" style="background: linear-gradient(135deg, #FFE66D 0%, #FF6B35 100%); display: flex; align-items: center; justify-content: center; font-size: 4rem;">
                                        🐱
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="cat-card-content">
                                <h3 class="cat-card-title"><?php echo htmlspecialchars($cat['name']); ?></h3>
                                <div class="cat-card-info">
                                    <span><strong>Breed:</strong> <?php echo htmlspecialchars($cat['breed'] ?: 'Mixed'); ?></span>
                                    <span><strong>Color:</strong> <?php echo htmlspecialchars($cat['color']); ?></span>
                                    <span><strong>Age:</strong> <?php echo htmlspecialchars($cat['age']); ?> years</span>
                                    <span><strong>Gender:</strong> <?php echo ucfirst($cat['gender']); ?></span>
                                </div>
                                <div style="display: flex; gap: 0.5rem; margin-top: 1rem;">
                                    <a href="my-cats.php?action=edit&id=<?php echo $cat['id']; ?>" class="btn btn-secondary" style="flex: 1; justify-content: center; font-size: 0.9rem; padding: 0.6rem 1rem;">
                                        Edit
                                    </a>
                                    <a href="report-lost.php?cat_id=<?php echo $cat['id']; ?>" class="btn btn-primary" style="flex: 1; justify-content: center; font-size: 0.9rem; padding: 0.6rem 1rem;">
                                        Report Lost
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Lost Reports -->
            <?php if (!empty($reports)): ?>
            <div style="background: var(--bg-white); padding: 2rem; border-radius: 20px; box-shadow: 0 4px 20px var(--shadow);">
                <h2 style="margin-bottom: 1.5rem;">My Lost Reports</h2>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Cat</th>
                                <th>Lost Date</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reports as $report): ?>
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 1rem;">
                                        <?php if ($report['photo']): ?>
                                            <img src="uploads/cats/<?php echo htmlspecialchars($report['photo']); ?>" 
                                                 style="width: 50px; height: 50px; border-radius: 10px; object-fit: cover;">
                                        <?php else: ?>
                                            <div style="width: 50px; height: 50px; background: var(--primary); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                                                🐱
                                            </div>
                                        <?php endif; ?>
                                        <strong><?php echo htmlspecialchars($report['cat_name']); ?></strong>
                                    </div>
                                </td>
                                <td><?php echo formatDate($report['lost_date']); ?></td>
                                <td><?php echo htmlspecialchars(substr($report['lost_location'], 0, 50)); ?></td>
                                <td>
                                    <?php if ($report['status'] === 'lost'): ?>
                                        <span style="background: var(--danger); color: white; padding: 0.3rem 0.8rem; border-radius: 15px; font-size: 0.85rem; font-weight: 600;">
                                            LOST
                                        </span>
                                    <?php else: ?>
                                        <span style="background: var(--success); color: white; padding: 0.3rem 0.8rem; border-radius: 15px; font-size: 0.85rem; font-weight: 600;">
                                            FOUND
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="cat-details.php?id=<?php echo $report['id']; ?>" class="btn btn-secondary" style="font-size: 0.85rem; padding: 0.4rem 1rem;">
                                        View
                                    </a>
                                    <?php if ($report['status'] === 'lost'): ?>
                                    <a href="mark-found.php?id=<?php echo $report['id']; ?>" 
                                       class="btn btn-primary" 
                                       style="font-size: 0.85rem; padding: 0.4rem 1rem; margin-left: 0.5rem;"
                                       onclick="return confirm('Mark this cat as found?');">
                                        Mark Found
                                    </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
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
</body>
</html>
