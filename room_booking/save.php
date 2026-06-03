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
?>
<!DOCTYPE html>
<html>
<head>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <link href='https://fonts.googleapis.com/css2?family=Inter:wght@400;700;800&display=swap' rel='stylesheet'>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; background: transparent; }
        .swal-clash-popup {
            background: #ffffff !important;
            border-radius: 20px !important;
            padding: 32px 28px 28px !important;
            border: 1px solid rgba(249,115,22,0.15) !important;
            box-shadow: 0 20px 60px rgba(0,0,0,0.12), 0 0 30px rgba(249,115,22,0.08) !important;
            width: 360px !important;
            font-family: Inter, sans-serif !important;
        }
        .swal-clash-popup .swal2-icon { display: none !important; }
        .swal-clash-title {
            font-size: 20px !important;
            font-weight: 800 !important;
            color: #0f172a !important;
            font-family: Inter, sans-serif !important;
            padding-top: 0 !important;
        }
        .swal-clash-html { margin-top: 6px !important; padding: 0 !important; }
        .swal-clash-actions {
            margin-top: 24px !important;
            justify-content: center !important;
            padding: 0 !important;
        }
        .swal-clash-confirm {
            background: #f97316 !important;
            color: #fff !important;
            border: none !important;
            border-radius: 10px !important;
            font-weight: 700 !important;
            font-size: 14px !important;
            padding: 11px 32px !important;
            font-family: Inter, sans-serif !important;
            box-shadow: 0 4px 14px rgba(249,115,22,0.4) !important;
            margin: 0 !important;
        }
        .swal-clash-confirm:hover {
            background: #ea580c !important;
            transform: translateY(-1px) !important;
        }
    </style>
</head>
<body>
<script>
    Swal.fire({
        title: 'Waktu Bertembung!',
        html: `
            <div style="text-align:center;">
                <div style="width:64px;height:64px;background:rgba(249,115,22,0.1);border-radius:50%;display:inline-flex;align-items:center;justify-content:center;margin-bottom:16px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#f97316" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                </div>
                <div style="font-family:Inter,sans-serif;font-size:14px;color:#64748b;line-height:1.8;">
                    Bilik ini <strong style="color:#ef4444;">sudah ditempah</strong><br>
                    pada waktu yang dipilih.<br>
                    <span style="color:#f97316;font-weight:700;">Sila pilih waktu lain.</span>
                </div>
            </div>
        `,
        confirmButtonText: '← Cuba Semula',
        backdrop: 'rgba(15,23,42,0.85)',
        customClass: {
            popup: 'swal-clash-popup',
            title: 'swal-clash-title',
            htmlContainer: 'swal-clash-html',
            confirmButton: 'swal-clash-confirm',
            actions: 'swal-clash-actions'
        }
    }).then(() => { window.history.back(); });
</script>
</body>
</html>
<?php
exit;
    }

    $sql = "INSERT INTO bookings (teacher_name, room_id, booking_date, start_time, end_time, purpose, subject, student_group, remarks, is_permanent, day_of_week, package_name) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
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
