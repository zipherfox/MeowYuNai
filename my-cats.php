<?php
require_once 'includes/config.php';
requireLogin();

$userId = getUserId();
$error = '';
$success = '';
$action = $_GET['action'] ?? 'list';
$catId = $_GET['id'] ?? null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $breed = sanitize($_POST['breed']);
    $color = sanitize($_POST['color']);
    $age = intval($_POST['age']);
    $gender = sanitize($_POST['gender']);
    $description = sanitize($_POST['description']);
    $microchipId = sanitize($_POST['microchip_id']);
    $distinctiveFeatures = sanitize($_POST['distinctive_features']);
    
    // Handle photo upload
    $photo = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $photo = uploadPhoto($_FILES['photo']);
    }
    
    if ($action === 'add') {
        $sql = "INSERT INTO cats (user_id, name, breed, color, age, gender, description, photo, microchip_id, distinctive_features) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isssisssss", $userId, $name, $breed, $color, $age, $gender, $description, $photo, $microchipId, $distinctiveFeatures);
        
        if ($stmt->execute()) {
            $success = 'Cat added successfully!';
            header('Location: my-cats.php');
            exit;
        } else {
            $error = 'Failed to add cat.';
        }
    } elseif ($action === 'edit' && $catId) {
        // Get existing photo
        $existingPhotoQuery = "SELECT photo FROM cats WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($existingPhotoQuery);
        $stmt->bind_param("ii", $catId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $existingCat = $result->fetch_assoc();
            $photoToUse = $photo ?: $existingCat['photo'];
            
            $sql = "UPDATE cats SET name = ?, breed = ?, color = ?, age = ?, gender = ?, 
                    description = ?, photo = ?, microchip_id = ?, distinctive_features = ? 
                    WHERE id = ? AND user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssissssssii", $name, $breed, $color, $age, $gender, $description, $photoToUse, $microchipId, $distinctiveFeatures, $catId, $userId);
            
            if ($stmt->execute()) {
                $success = 'Cat updated successfully!';
                header('Location: my-cats.php');
                exit;
            } else {
                $error = 'Failed to update cat.';
            }
        }
    }
}

// Handle delete
if ($action === 'delete' && $catId) {
    $sql = "DELETE FROM cats WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $catId, $userId);
    
    if ($stmt->execute()) {
        $success = 'Cat deleted successfully.';
        header('Location: my-cats.php');
        exit;
    }
}

// Get cats for listing
$catsQuery = "SELECT * FROM cats WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($catsQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$cats = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get specific cat for editing
$editCat = null;
if ($action === 'edit' && $catId) {
    $editQuery = "SELECT * FROM cats WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($editQuery);
    $stmt->bind_param("ii", $catId, $userId);
    $stmt->execute();
    $editCat = $stmt->get_result()->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cats - PawFinder</title>
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

    <?php if ($action === 'list'): ?>
    <section class="dashboard">
        <div class="container">
            <div class="dashboard-header">
                <h1>My Cats</h1>
                <a href="my-cats.php?action=add" class="btn btn-primary">➕ Add New Cat</a>
            </div>

            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <?php if (empty($cats)): ?>
                <div style="background: var(--bg-white); padding: 4rem; border-radius: 20px; box-shadow: 0 4px 20px var(--shadow); text-align: center;">
                    <div style="font-size: 5rem; margin-bottom: 1rem;">🐱</div>
                    <h2>No cats registered yet</h2>
                    <p style="color: var(--text-light); margin-bottom: 2rem;">Start by adding your first cat to the system.</p>
                    <a href="my-cats.php?action=add" class="btn btn-primary">Add Your First Cat</a>
                </div>
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
                                <?php if ($cat['microchip_id']): ?>
                                <span><strong>Microchip:</strong> <?php echo htmlspecialchars($cat['microchip_id']); ?></span>
                                <?php endif; ?>
                            </div>
                            <?php if ($cat['description']): ?>
                            <p class="cat-card-description">
                                <?php echo htmlspecialchars(substr($cat['description'], 0, 100)); ?>...
                            </p>
                            <?php endif; ?>
                            <div style="display: flex; gap: 0.5rem; margin-top: 1rem;">
                                <a href="my-cats.php?action=edit&id=<?php echo $cat['id']; ?>" class="btn btn-secondary" style="flex: 1; justify-content: center; font-size: 0.9rem; padding: 0.6rem 1rem;">
                                    ✏️ Edit
                                </a>
                                <a href="report-lost.php?cat_id=<?php echo $cat['id']; ?>" class="btn btn-primary" style="flex: 1; justify-content: center; font-size: 0.9rem; padding: 0.6rem 1rem;">
                                    🚨 Report Lost
                                </a>
                                <a href="my-cats.php?action=delete&id=<?php echo $cat['id']; ?>" 
                                   onclick="return confirm('Are you sure you want to delete this cat profile?');"
                                   class="btn btn-outline" style="padding: 0.6rem 1rem; font-size: 0.9rem;">
                                    🗑️
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <?php else: ?>
    <!-- Add/Edit Form -->
    <div class="form-container">
        <h2><?php echo $action === 'add' ? 'Add New Cat' : 'Edit Cat'; ?></h2>
        <p style="color: var(--text-light); margin-bottom: 2rem;">
            <?php echo $action === 'add' ? 'Register your cat in the system' : 'Update your cat\'s information'; ?>
        </p>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Cat Name *</label>
                <input type="text" id="name" name="name" class="form-control" required
                       value="<?php echo htmlspecialchars($editCat['name'] ?? ''); ?>">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="breed">Breed</label>
                    <input type="text" id="breed" name="breed" class="form-control" 
                           placeholder="e.g., Persian, Siamese"
                           value="<?php echo htmlspecialchars($editCat['breed'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="color">Color *</label>
                    <input type="text" id="color" name="color" class="form-control" required
                           placeholder="e.g., Orange, Black, White"
                           value="<?php echo htmlspecialchars($editCat['color'] ?? ''); ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="age">Age (years) *</label>
                    <input type="number" id="age" name="age" class="form-control" required min="0" max="30"
                           value="<?php echo htmlspecialchars($editCat['age'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="gender">Gender *</label>
                    <select id="gender" name="gender" class="form-control" required>
                        <option value="male" <?php echo ($editCat['gender'] ?? '') === 'male' ? 'selected' : ''; ?>>Male</option>
                        <option value="female" <?php echo ($editCat['gender'] ?? '') === 'female' ? 'selected' : ''; ?>>Female</option>
                        <option value="unknown" <?php echo ($editCat['gender'] ?? '') === 'unknown' ? 'selected' : ''; ?>>Unknown</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="microchip_id">Microchip ID</label>
                <input type="text" id="microchip_id" name="microchip_id" class="form-control"
                       placeholder="15-digit microchip number"
                       value="<?php echo htmlspecialchars($editCat['microchip_id'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="photo">Cat Photo</label>
                <?php if ($editCat && $editCat['photo']): ?>
                    <div style="margin-bottom: 1rem;">
                        <img src="uploads/cats/<?php echo htmlspecialchars($editCat['photo']); ?>" 
                             style="max-width: 200px; border-radius: 10px;">
                    </div>
                <?php endif; ?>
                <input type="file" id="photo" name="photo" class="form-control" accept="image/*">
            </div>

            <div class="form-group">
                <label for="distinctive_features">Distinctive Features</label>
                <textarea id="distinctive_features" name="distinctive_features" class="form-control" 
                          placeholder="Any unique markings, scars, or features..."><?php echo htmlspecialchars($editCat['distinctive_features'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label for="description">General Description</label>
                <textarea id="description" name="description" class="form-control"
                          placeholder="Personality, behavior, habits..."><?php echo htmlspecialchars($editCat['description'] ?? ''); ?></textarea>
            </div>

            <div style="display: flex; gap: 1rem;">
                <button type="submit" class="btn btn-primary" style="flex: 1; justify-content: center;">
                    <?php echo $action === 'add' ? '➕ Add Cat' : '💾 Save Changes'; ?>
                </button>
                <a href="my-cats.php" class="btn btn-outline" style="flex: 1; justify-content: center;">
                    Cancel
                </a>
            </div>
        </form>
    </div>
    <?php endif; ?>

    <footer class="footer">
        <div class="container">
            <div class="footer-bottom">
                <p>&copy; 2025 PawFinder. All rights reserved. Made with ❤️ for cats.</p>
            </div>
        </div>
    </footer>
</body>
</html>
