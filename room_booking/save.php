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
echo "
<!DOCTYPE html>
<html>
<head>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <link href='https://fonts.googleapis.com/css2?family=Inter:wght@700;800&display=swap' rel='stylesheet'>
</head>
<body>
<script>
    Swal.fire({
        icon: 'error',
        title: '🚨 Clash Waktu!',
        html: `
            <div style='font-family:Inter,sans-serif; font-size:14px; color:#64748b; line-height:1.8;'>
                Bilik ini <strong style='color:#ef4444;'>sudah ditempah</strong><br>
                pada waktu yang sama.<br><br>
                <span style='background:#fff1f2; padding:6px 14px; border-radius:8px; font-weight:700; color:#dc2626; font-size:13px; border:1px solid #fecaca;'>
                    ⏰ Sila pilih waktu lain
                </span>
            </div>
        `,
        confirmButtonText: '← Cuba Semula',
        confirmButtonColor: '#f97316',
        background: '#fff',
        showClass: {
            popup: 'animate__animated animate__shakeX'
        },
        customClass: {
            popup: 'clash-popup',
            title: 'clash-title',
            confirmButton: 'clash-btn'
        }
    }).then(() => { window.history.back(); });
</script>
<style>
    .clash-popup {
        border-radius: 20px !important;
        box-shadow: 0 25px 50px rgba(0,0,0,0.2),
                    0 0 40px rgba(239,68,68,0.25),
                    0 0 80px rgba(239,68,68,0.1) !important;
        border: 1px solid rgba(239,68,68,0.15) !important;
    }
    .clash-title {
        font-size: 22px !important;
        font-weight: 800 !important;
        color: #0f172a !important;
    }
    .clash-btn {
        border-radius: 10px !important;
        font-weight: 700 !important;
        padding: 12px 28px !important;
        box-shadow: 0 0 15px rgba(249,115,22,0.4) !important;
    }
</style>
</body>
</html>";
exit;        exit;
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
    $safe_date = preg_replace('/[^0-9\-]/', '', $booking_date);
    header("Location: index.php?date=" . $safe_date);
} else {
    echo "Ralat berlaku. Sila hubungi admin.";
}
}