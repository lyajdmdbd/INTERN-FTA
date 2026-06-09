<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'config.php';

// Kendalikan permohonan OPTIONS (CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);
$id = (int)($data['id'] ?? 0);

if ($id > 0) {
    // Guna prepared statement untuk keselamatan
    $stmt = $conn->prepare("DELETE FROM tempahan WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'ok', 'message' => 'Tempahan berjaya dipadam']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal memadam: ' . $conn->error]);
    }
    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'ID tempahan tidak sah']);
}