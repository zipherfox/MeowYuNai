<?php
require_once 'includes/config.php';
requireLogin();

$reportId = $_GET['id'] ?? null;
$userId = getUserId();

if (!$reportId) {
    header('Location: dashboard.php');
    exit;
}

// Verify the report belongs to the user
$verifySql = "SELECT id, status FROM lost_reports WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($verifySql);
$stmt->bind_param("ii", $reportId, $userId);
$stmt->execute();
$report = $stmt->get_result()->fetch_assoc();

if (!$report) {
    $_SESSION['error'] = 'Report not found or you do not have permission.';
    header('Location: dashboard.php');
    exit;
}

if ($report['status'] === 'found') {
    $_SESSION['error'] = 'This cat is already marked as found.';
    header('Location: dashboard.php');
    exit;
}

// Update the report
$updateSql = "UPDATE lost_reports SET status = 'found', found_date = NOW() WHERE id = ?";
$stmt = $conn->prepare($updateSql);
$stmt->bind_param("i", $reportId);

if ($stmt->execute()) {
    $_SESSION['success'] = 'Congratulations! Your cat has been marked as found!';
} else {
    $_SESSION['error'] = 'Failed to update status. Please try again.';
}

header('Location: dashboard.php');
exit;
?>
