<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $teacher_name = $_POST['teacher_name'];
    $room_id = $_POST['room_id'];
    $booking_date = $_POST['booking_date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $purpose = $_POST['purpose'];
    $subject = $_POST['subject'];
    $student_group = $_POST['student_group'];
    $remarks = $_POST['remarks'];
    $package_name = $_POST['package_name'];
    $is_permanent = isset($_POST['is_permanent']) ? 1 : 0;
    // --- LOGIK HARI ---
    $day_names = [
        'Monday' => 'ISNIN', 'Tuesday' => 'SELASA', 'Wednesday' => 'RABU',
        'Thursday' => 'KHAMIS', 'Friday' => 'JUMAAT', 'Saturday' => 'SABTU', 'Sunday' => 'AHAD'
    ];
    $day_of_week = $day_names[date('l', strtotime($booking_date))];

    // --- CHECK CLASH ---
    $check_sql = "SELECT id FROM bookings 
                WHERE room_id = ? 
                AND day_of_week = ? 
                AND start_time < ? 
                AND end_time > ?";

    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("isss", $room_id, $day_of_week, $end_time, $start_time);
    $check_stmt->execute();

    if ($check_stmt->get_result()->num_rows > 0) {
        echo "<script>alert('Alamak, Clash! Waktu ni dah ada kelas lain.'); window.history.back();</script>";
        exit;
    }

    $sql = "INSERT INTO bookings (teacher_name, room_id, booking_date, start_time, end_time, purpose, subject, student_group, remarks, is_permanent, day_of_week, package_name) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
        // Bind param kena tambah satu "s" kat belakang untuk $package
    $stmt->bind_param("sisssssssiss", 
    $teacher_name, 
    $room_id, 
    $booking_date, 
    $start_time, 
    $end_time, 
    $purpose, 
    $subject, 
    $student_group, 
    $remarks, 
    $is_permanent, 
    $day_of_week,
    $package_name
);

    if ($stmt->execute()) {
    header("Location: index.php?date=" . $booking_date);
} else {
    echo "Error: " . $stmt->error;
}
}