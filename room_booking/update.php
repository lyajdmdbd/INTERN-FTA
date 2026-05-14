<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $teacher_name = $_POST['teacher_name'];
    $room_id = $_POST['room_id'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $purpose = $_POST['purpose'];
    $subject = $_POST['subject'];
    $student_group = $_POST['student_group'];
    $remarks = $_POST['remarks'];
    
    // --- AMBIL DATA PAKEJ BARU ---
    $package_name = $_POST['package_name'];
    // Check clash tapi abaikan ID tempahan sekarang
    $check_sql = "SELECT id FROM bookings WHERE room_id = ? AND booking_date = (SELECT booking_date FROM bookings WHERE id = ?) AND start_time < ? AND end_time > ? AND id != ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("iissi", $room_id, $id, $end_time, $start_time, $id);
    $check_stmt->execute();
    
    if ($check_stmt->get_result()->num_rows > 0) {
        echo "
        <!DOCTYPE html>
        <html>
        <head>
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
            <style>body { font-family: 'Inter', sans-serif; background: #f8fafc; }</style>
        </head>
        <body>
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Alamak, Clash!',
                    text: 'Bilik sudah ditempah pada waktu tersebut.',
                    confirmButtonColor: '#f97316',
                }).then(() => { window.history.back(); });
            </script>
        </body>
        </html>";
        exit;
    }

    // --- UPDATE DATA (DAH TAMBAH COLUMN PACKAGE) ---
    $sql = "UPDATE bookings SET teacher_name=?, room_id=?, start_time=?, end_time=?, purpose=?, subject=?, student_group=?, remarks=?, package_name=? WHERE id=?";
    $stmt = $conn->prepare($sql);

    // Bind param kena ada 10 parameter (tambah 's' untuk package)
    $stmt->bind_param("sisssssssi", 
    $teacher_name, 
    $room_id, 
    $start_time, 
    $end_time, 
    $purpose, 
    $subject, 
    $student_group, 
    $remarks, 
    $package_name, 
    $id
);

    if ($stmt->execute()) {
    // Ambil tarikh asal untuk redirect balik
    $res = $conn->query("SELECT booking_date FROM bookings WHERE id = $id");
    $row = $res->fetch_assoc();
    header("Location: index.php?date=" . $row['booking_date']);
} else {
    echo "Update Failed: " . $stmt->error;
}
}