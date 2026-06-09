<?php
require_once 'config.php';
if(isset($_GET['id'])) {
    $id = intval($_GET['id']);
    // Tangkap tarikh dari URL supaya boleh hantar balik nanti
    $date = preg_replace('/[^0-9\-]/', '', $_GET['date'] ?? '');
    
    $conn->query("DELETE FROM bookings WHERE id = $id");
    
    // Redirect balik ke tarikh yang sama
    header("Location: index.php?date=" . $date);
    exit;
}
?>