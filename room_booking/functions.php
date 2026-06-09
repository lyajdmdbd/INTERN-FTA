<?php
function overlaps($conn, $room_id, $date, $start_time, $end_time) {
    $sql = "SELECT * FROM bookings WHERE room_id = ? AND booking_date = ? AND (start_time < ? AND end_time > ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('isss', $room_id, $date, $end_time, $start_time);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

function get_time_slots() {
    $slots = [];
    $current = strtotime('08:00');
    $end = strtotime('22:00');
    while ($current <= $end) {
        $slots[] = date('h:i A', $current);
        $current = strtotime('+30 minutes', $current);
    }
    return $slots;
}

function sanitize($conn, $data) {
    return mysqli_real_escape_string($conn, htmlspecialchars(strip_tags(trim($data))));
}
?>