<?php
require_once 'config.php';
require_once 'functions.php';

date_default_timezone_set('Asia/Kuala_Lumpur');
$today = date('Y-m-d');
$now_time = date('H:i:s');
$sel_date = $_GET['date'] ?? $today;

// --- 1. AMBIL DATA BILIK (Ini yang error tadi) ---
$rooms = $conn->query("SELECT * FROM rooms ORDER BY level, room_name")->fetch_all(MYSQLI_ASSOC);

// --- 2. TENTUKAN NAMA HARI ---
$day_names = [
    'Monday' => 'ISNIN', 'Tuesday' => 'SELASA', 'Wednesday' => 'RABU',
    'Thursday' => 'KHAMIS', 'Friday' => 'JUMAAT', 'Saturday' => 'SABTU', 'Sunday' => 'AHAD'
];

$current_day_en = date('l', strtotime($sel_date)); 
$sel_day = $day_names[$current_day_en]; // Pastikan $day_names dah ditakrifkan kat atas

// Cari bahagian $sql = "SELECT ...
$sql = "SELECT b.*, r.room_name, r.level, b.package_name
        FROM bookings b 
        JOIN rooms r ON b.room_id = r.id 
        WHERE (b.booking_date = ? AND b.is_permanent = 0) 
           OR (b.day_of_week = ? AND b.is_permanent = 1)
        ORDER BY b.start_time ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param('ss', $sel_date, $sel_day); // $sel_day mestilah 'ISNIN', 'SELASA', dll.
$stmt->execute();
$bookings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$occupied_ids = []; 
$ongoing_bookings = []; 

foreach ($bookings as $b) {
    if ($sel_date == $today) {
        if ($now_time >= $b['start_time'] && $now_time < $b['end_time']) {
            $occupied_ids[] = $b['room_id'];
            $ongoing_bookings[] = $b['room_id'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Sistem Tempahan Bilik | Fast Track</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="stylesheet" href="style.css?v=<?= time(); ?>">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        /* Style Tambahan untuk Badge Merah Berkelip */
        .ongoing-status-badge {
            background: #fee2e2;
            color: #ef4444;
            padding: 4px 10px;
            border-radius: 50px;
            font-size: 10px;
            font-weight: 800;
            border: 1px solid #fecaca;
            animation: pulse-red 2s infinite;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        @keyframes pulse-red {
            0% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.7; transform: scale(0.95); }
            100% { opacity: 1; transform: scale(1); }
        }

        .search-filter-wrapper { margin-bottom: 25px; display: flex; flex-direction: column; gap: 15px; background: #f8fafc; padding: 15px; border-radius: 12px; border: 1px solid #e2e8f0; }
        .search-box { position: relative; display: flex; align-items: center; }
        .search-box i { position: absolute; left: 15px; color: #94a3b8; width: 18px; }
        .search-box input { width: 100%; padding: 12px 12px 12px 45px; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 14px; }
        .pill { padding: 8px 16px; border-radius: 20px; border: 1px solid #e2e8f0; background: white; font-size: 13px; font-weight: 600; cursor: pointer; color: #64748b; }
        .pill.active { background: #3b82f6; color: white; border-color: #3b82f6; }
        .aesthetic-date { font-family: 'Inter', sans-serif; font-weight: 500; letter-spacing: 0.5px; color: #64748b; font-size: 0.6em; background: #ffffff; padding: 6px 16px; border-radius: 50px; border: 1px solid #e2e8f0; box-shadow: 0 2px 4px rgba(0,0,0,0.02); text-transform: uppercase; }
        .btn-group { display: flex; gap: 8px; }
        .btn-action { padding: 8px 12px; border-radius: 8px; border: none; cursor: pointer; display: flex; align-items: center; gap: 5px; font-size: 12px; font-weight: 600; }
        .btn-action.delete { background: #fee2e2; color: #dc2626; }
        /* Buat skrin bergerak smooth bila tekan link anchor */
        html {
            scroll-behavior: smooth;
        }

        /* Beri sedikit ruang atas supaya header tak tutup kad bila sampai */
        .booking-card {
            scroll-margin-top: 100px;
        }

        /* Efek highlight bila kad itu "aktif" selepas ditekan */
        .booking-card:target {
            border: 2px solid var(--ft-orange);
            background-color: #fffaf5 !important;
            transform: scale(1.02);
            box-shadow: 0 10px 20px rgba(245, 130, 32, 0.15);
        }
    </style>
</head>
<body>
    <!-- Video Background -->
<video autoplay muted loop playsinline id="bg-video" style="
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    object-fit: cover;
    z-index: -1;
    opacity: 0.9;
">
    <source src="bgvideo2.mp4" type="video/mp4">
</video>
<header class="hero">
    <div class="header-container">
        <div class="brand" style="display: flex; align-items: center; gap: 20px;">
        <!-- Logo diletakkan di sebelah teks -->
        <img src="Picture1.png" alt="FT Logo" style="height: 60px; width: auto;">
        <div style="border-left: 2px solid rgba(255,255,255,0.2); padding-left: 20px;">
            <h1 style="margin:0; font-size: 24px;">SISTEM TEMPAHAN BILIK</h1>
            <p style="margin:0; opacity: 0.8; font-size: 12px; letter-spacing: 1px;">FAST TRACK EDUCATION CENTRE</p>
        </div>
    </div>
        <div class="header-actions" style="display: flex; gap: 15px; align-items: center;">
        <!-- Butang Balik ke Portal (TAMBAH NI) -->
        <a href="../index.php" style="text-decoration:none;">
            <button class="btn-action" style="background: #1e293b; color: white; padding: 10px 15px; height: 42px;">
                <i data-lucide="layout-grid" style="width: 18px;"></i>
                <span>Kembali</span>
            </button>
        </a>
    <input type="date" value="<?= $sel_date ?>" onchange="location='index.php?date='+this.value" class="date-input">
    <button class="btn-primary" onclick="openModal('modalTempah')">+ Tempah Bilik</button>
</div>
    </div>
</header>

<div class="main-layout">
    <div class="content-left">
        <h2 class="section-title" style="display: flex; align-items: center; gap: 15px; margin-bottom: 25px;">
            <i data-lucide="monitor" style="color: #ffffff;"></i> 
            <span style="font-weight: 800; color: #ffffff;">Status Bilik Hari Ini</span>
            <span class="aesthetic-date"><?= date('D, d M Y', strtotime($sel_date)) ?></span>
        </h2>

        <div class="room-grid">
            <?php foreach($rooms as $r): 
        // 1. Ambil tempahan bilik ni untuk hari dipilih
        $room_slots = array_filter($bookings, function($b) use ($r) {
            return $b['room_id'] == $r['id'];
        });

        // Ganti baris $upcoming_or_ongoing = array_filter(...) dengan ini:
        $upcoming_or_ongoing = $room_slots;

        // 3. JIKA SEMUA TEMPAHAN DAH LEPAS (atau takde tempahan), GHAIBKAN CARD
        if (empty($upcoming_or_ongoing)) continue; 

        // Susun jadual ikut masa
        usort($upcoming_or_ongoing, function($a, $b) { 
            return strcmp($a['start_time'], $b['start_time']); 
        });

        $is_booked = true;
        $is_now_using = in_array($r['id'], $ongoing_bookings);
        
        // Tentukan warna border (Merah jika sedang guna, Oren jika akan datang)
        $borderColor = $is_now_using ? '#ef4444' : '#f97316';
    ?>
            <div class="room-card <?= $is_booked ? 'busy' : '' ?>" style="border-left: 5px solid <?= $is_booked ? ($is_now_using ? '#ef4444' : '#f97316') : '#22c55e' ?>;">
                <div class="card-header" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                    <div class="room-info">
                        <h3 style="margin: 0; font-size: 18px; font-weight: 800; color: #1e293b;"><?= $r['room_name'] ?></h3>
                        <span style="font-size: 11px; color: #94a3b8; font-weight: 700; text-transform: uppercase;"><?= $r['level'] ?></span>                    
                    </div>
                    <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 5px;">
                        <span class="status-pill <?= $is_booked ? 'booked' : 'empty' ?>">
                            <?= $is_booked ? 'DITEMPAH' : 'KOSONG' ?>
                        </span>
                        <?php if($is_now_using): ?>
                        <span class="ongoing-status-badge">
                            <span class="status-pulse-red"></span> 
                        <span class="status-text-guna">SEDANG GUNA</span>
                        </span>
                    <?php endif; ?>
                    </div>
                </div>

                <?php if (!empty($room_slots)): ?>
                    <div class="card-details-container" style="margin-top: -5px; gap: 4px; border-top: 1px solid #f1f5f9; padding-top: 8px;">
                        <?php if (!empty($upcoming_or_ongoing)): ?>
                        <div class="card-details-container">
                            <?php foreach ($upcoming_or_ongoing as $slot): ?>
                            <a href="#booking-<?= $slot['id'] ?>" style="text-decoration: none; color: inherit; display: block;">
                            <div class="mini-session-row" style="cursor: pointer;">
                                <div class="mini-time"><?= date('H:i', strtotime($slot['start_time'])) ?> - <?= date('H:i', strtotime($slot['end_time'])) ?></div>
                                <div class="mini-teacher"><?= $slot['teacher_name'] ?></div>
                                <div class="mini-details">
                                    <?php 
                                        $p_class = strtolower(explode('/', trim($slot['purpose']))[0]); 
                                    ?>
                                    <span class="mini-purpose purpose-bg-<?= $p_class ?>">
                                        <?= (trim($slot['purpose']) == 'MEETING/LAIN-LAIN') ? 'MEETING' : $slot['purpose'] ?>
                                    </span>
                                    <span style="color: #64748b; font-weight: 500;">
                                    <?php if(trim($slot['purpose']) == 'MEETING/LAIN-LAIN'): ?>
                                        <?= htmlspecialchars($slot['remarks']) ?>
                                    <?php else: ?>
                                    <?= htmlspecialchars($slot['subject']) ?> (<?= htmlspecialchars($slot['student_group']) ?>)<?php if(!empty($slot['package_name'])): ?> • <?= htmlspecialchars($slot['package_name']) ?><?php endif; ?>
                                        <?php endif; ?>
                                </span>
                                </div>
                            </div>
                            </a>
                        <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div style="margin-top: 10px; font-size: 11px; color: #94a3b8; font-style: italic;">Bilik sedia untuk ditempah.</div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>

        <h2 class="section-title" style="margin-top: 50px; color: #ffffff;"><i data-lucide="calendar" style="color: #ffffff;"></i> Jadual Hari Ini</h2>
        <div class="search-filter-wrapper">
            <div class="search-box">
                <i data-lucide="search"></i>
                <input type="text" id="bookingSearch" placeholder="Cari nama guru, subjek atau bilik..." onkeyup="filterBookings()">
            </div>
            <div class="filter-pills">
                <button class="pill active" onclick="filterStatus('all', this)">Semua</button>
                <button class="pill" onclick="filterStatus('ongoing', this)">Sedang Berlangsung</button>
                <button class="pill" onclick="filterStatus('standby', this)">Standby (Akan Datang)</button>
            </div>
        </div>

        <div class="booking-container">
    <?php if(empty($bookings)): ?>
        <div style="text-align: center; padding: 40px; background: white; border-radius: 15px; color: #94a3b8; border: 1px dashed #e2e8f0;">
            <i data-lucide="calendar-x" style="width: 40px; height: 40px; margin-bottom: 10px; opacity: 0.5;"></i>
            <p style="font-style: italic; font-size: 14px;">Tiada tempahan untuk tarikh ini.</p>
        </div>
    <?php else: ?>
        <?php foreach($bookings as $b): 
            $is_past = ($sel_date == $today && $now_time > $b['end_time']);
            $is_ongoing = ($sel_date == $today && $now_time >= $b['start_time'] && $now_time <= $b['end_time']);
        ?>
            <!-- Pastikan class 'is-ongoing-card' ada di sini[cite: 13] -->
            <div id="booking-<?= $b['id'] ?>" 
                 class="booking-card <?= $is_past ? 'past-booking' : '' ?> <?= $is_ongoing ? 'is-ongoing-card' : '' ?>" 
                 style="background: white; padding: 20px; border-radius: 15px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border-left: 5px solid <?= $is_past ? '#cbd5e1' : ($is_ongoing ? '#ef4444' : '#f97316') ?>; margin-bottom: 15px; opacity: <?= $is_past ? '0.6' : '1' ?>;">
                
                <div style="width: 140px;">
                    <div style="font-weight: 800; color: #1e293b; font-size: 15px;"><?= date('H:i', strtotime($b['start_time'])) ?> - <?= date('H:i', strtotime($b['end_time'])) ?></div>
                    <?php if($is_ongoing): ?>
                        <span class="ongoing-label" style="color: #ef4444; font-size: 10px; font-weight: 800;">SEDANG GUNA</span>
                    <?php endif; ?>
                </div>

                <div style="flex: 1; padding: 0 20px;">
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 4px;">
                        <span style="font-weight: 700; font-size: 16px; color: #0f172a;"><?= $b['room_name'] ?></span>
                        <?php 
                            // Ambil perkataan pertama (pcc, po, center, etc) untuk panggil warna pastel
                            $p_type = strtolower(explode('/', trim($b['purpose']))[0]); 
                        ?>
                        <span class="mini-purpose purpose-bg-<?= $p_type ?>">
                        <?= (trim($b['purpose']) == 'MEETING/LAIN-LAIN') ? 'MEETING' : $b['purpose'] ?>
                    </span>
                        
                    </div>
                    <div style="color: #64748b; font-size: 14px; font-weight: 500;">
                    <?= $b['teacher_name'] ?> • 
                    <span style="color: #94a3b8;">
                        <?php if(trim($b['purpose']) == 'MEETING/LAIN-LAIN'): ?>
                            <?= htmlspecialchars($b['remarks']) ?>
                        <?php else: ?>
                        <?= htmlspecialchars($b['subject']) ?> (<?= htmlspecialchars($b['student_group']) ?>)<?php if(!empty($b['package_name'])): ?> • <?= htmlspecialchars($b['package_name']) ?><?php endif; ?>                        <?php endif; ?>
                    </span>
                </div>
                </div>

                <?php if(!$is_past): ?>
                    <div class="btn-group">
                        <button class="btn-action" onclick="openEditModal(<?= htmlspecialchars(json_encode($b)) ?>)" style="background: #f8fafc; border: 1px solid #e2e8f0; color: #64748b;">
                            <i data-lucide="edit-3" style="width: 16px;"></i>
                        </button>
                        <a href="javascript:void(0)" class="btn-action delete" onclick="confirmDelete(<?= $b['id'] ?>)">
                            <i data-lucide="trash-2" style="width: 16px;"></i>
                        </a>
                    </div>
                <?php endif; ?>
            </div> <!-- Penutup booking-card di sini, baru dia tak lari! -->
        <?php endforeach; ?>
    <?php endif; ?>
</div>
    </div> 
    
    <aside class="sidebar-right" style="position: relative; align-self: flex-start;">
        <div class="sidebar-inner">
                    <h2 class="section-title" style="display: flex; align-items: center; justify-content: space-between; width: 100%; margin-bottom: 25px;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <i data-lucide="rss" style="color: white; width: 22px; height: 22px;" stroke-width="2.5"></i> 
                        <span style="font-weight: 800; color: white; font-size: 19px; white-space: nowrap; letter-spacing: -0.5px;">Bilik Kosong Sekarang</span>
                    </div>
                    
                    <span id="live-clock" class="aesthetic-clock" style="margin-left: 20px;">00:00:00</span>
                </h2>     
                <?php for($lvl=1; $lvl<=3; $lvl++): ?>
                <div class="lvl-header">ARAS <?= $lvl ?></div>                
                <div class="side-room-list" style="display: flex; flex-direction: column; gap: 8px;">
                    <?php 
                    $found = 0; $level_str = "Aras " . $lvl;
                    foreach($rooms as $r) {
                        if($r['level'] == $level_str && !in_array($r['id'], $occupied_ids)) {
                            echo "<div class='side-room-item'>{$r['room_name']}</div>";
                            $found++;
                        }
                    }
                    if($found == 0) echo "<p style='font-size: 12px; color: #94a3b8; font-style: italic;'>Penuh / Tiada</p>";
                    ?>
                </div>
            <?php endfor; ?>
        </div>
    </aside>
</div> 

<div id="modalTempah" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <div><h2>Tempah Bilik Sekarang</h2><p>Lengkapkan maklumat berikut</p></div>
            <button class="close-btn" onclick="closeModal('modalTempah')">&times;</button>
        </div>
        <form action="save.php" method="POST">
            <input type="hidden" name="booking_day" id="add_day" value="<?= date('l', strtotime($sel_date)) ?>">
            <div class="form-group"><label>Nama Guru</label><input type="text" name="teacher_name" placeholder="Cth: Cikgu Aleeya" required></div>
            <div class="form-row">
                <div class="form-group">
                    <label>Bilik</label>
                    <select name="room_id" required>
                        <option value="" disabled selected>Pilih Bilik</option>
                        <?php foreach($rooms as $r): 
                            $cap = "";
                            if($r['room_name'] == "AL HAMKA") $cap = " (2-3)"; // Tambah baris ni
                            elseif($r['room_name'] == "ALPHA") $cap = " (20->30)";
                            elseif($r['room_name'] == "HALL A") $cap = " (15-20)";
                            elseif($r['room_name'] == "BETA") $cap = " (6-8)";
                            elseif($r['room_name'] == "GAMMA") $cap = " (20->43)";
                            elseif($r['room_name'] == "HALL B") $cap = " (15-23)";
                            elseif($r['room_name'] == "DELTA") $cap = " (5-6)";
                            elseif($r['room_name'] == "AL FARABI") $cap = " (16)";
                            elseif($r['room_name'] == "AL GHAZALI") $cap = " (13)";
                            elseif($r['room_name'] == "MEZZANINE") $cap = " (16-20)";
                            elseif($r['room_name'] == "LECTURE ROOM 1") $cap = " (16-20)";
                            elseif($r['room_name'] == "LECTURE ROOM 2") $cap = " (16-20)";
                            elseif($r['room_name'] == "MARS") $cap = " (PERSONAL 1)";
                            elseif($r['room_name'] == "PLUTO") $cap = " (PERSONAL 1-2)";
                        ?>
                            <option value="<?= $r['id'] ?>"><?= $r['room_name'] . $cap ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group"><label>Tarikh</label><input type="date" name="booking_date" value="<?= $sel_date ?>" required></div>
            </div>
            <div class="form-row">
                <div class="form-group"><label>Masa Mula</label><input type="time" name="start_time" required></div>
                <div class="form-group"><label>Masa Tamat</label><input type="time" name="end_time" required></div>
            </div>
            <div class="form-group">
                <label>Tujuan</label>
                <select name="purpose" id="p_add" onchange="toggleForm('modalTempah')" required>
                    <option value="PCC">PCC</option><option value="OG">OG</option><option value="PO">PO</option><option value="CENTER">CENTER</option><option value="MEETING/LAIN-LAIN">MEETING/LAIN-LAIN</option>
                </select>
            </div>
            <div id="class_fields_modalTempah">
                <div class="form-row">
                    <div class="form-group"><label>Subjek</label><input type="text" name="subject" placeholder="Cth: BM / BI / MT"></div>
                    <div class="form-group"><label>Tingkatan/Darjah</label><input type="text" name="student_group" placeholder="Cth: T5 / D2 / T1 G2">
                        <small style="display: block; margin-top: 5px; color: #64748b; font-size: 11px; line-height: 1.3;">
                        *Nyatakan kumpulan kelas JIKA ADA 
                    </small>
                </div>
                </div>
                <div class="form-group"><label>Jenis Pakej</label><input type="text" name="package_name" placeholder="Cth: Standard / Gold / Silver"></div>
            </div>
            <div id="remarks_fields_modalTempah" style="display:none;">
                <div class="form-group"><label>Catatan</label><textarea name="remarks" rows="2" placeholder="Masukkan tujuan meeting..."></textarea></div>
            </div>
            <div style="background: #fff7ed; padding: 12px; border-radius: 10px; border: 1px solid #ffedd5; margin-bottom: 15px;">
            <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                <input type="checkbox" name="is_permanent" value="1" style="width: 18px; height: 18px;">
                <span style="font-weight: 700; color: #c2410c;">Set sebagai Jadual Tetap</span>
            </label>
            <small style="display: block; margin-left: 28px; color: #9a3412;">
                *Tanda ini jika kelas berulang setiap minggu pada hari yang sama.
            </small>
        </div>
            <div class="modal-footer"><button type="button" class="btn-secondary" onclick="closeModal('modalTempah')">Batal</button><button type="submit" class="btn-save">Simpan Tempahan</button></div>
        </form>
    </div>
</div>

<div id="modalEdit" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <div><h2>Edit Tempahan</h2><p>Kemaskini data</p></div>
            <button class="close-btn" onclick="closeModal('modalEdit')">&times;</button>
        </div>
        <form action="update.php" method="POST">
            <input type="hidden" name="id" id="edit_id">
            <input type="hidden" name="booking_day" id="edit_day">
            <div class="form-group"><label>Nama Guru</label><input type="text" name="teacher_name" id="edit_teacher" placeholder="Nama Guru" required></div>
            <div class="form-row">
                <div class="form-group">
                    <label>Bilik</label>
                    <select name="room_id" id="edit_room" required>
                        <?php foreach($rooms as $r): 
                            $cap = "";
                            if($r['room_name'] == "ALPHA") $cap = " (20->30)";
                            elseif($r['room_name'] == "HALL A") $cap = " (15-20)";
                            elseif($r['room_name'] == "BETA") $cap = " (6-8)";
                            elseif($r['room_name'] == "GAMMA") $cap = " (20->43)";
                            elseif($r['room_name'] == "HALL B") $cap = " (15-23)";
                            elseif($r['room_name'] == "DELTA") $cap = " (5-6)";
                            elseif($r['room_name'] == "AL FARABI") $cap = " (16)";
                            elseif($r['room_name'] == "AL GHAZALI") $cap = " (13)";
                            elseif($r['room_name'] == "MEZZANINE") $cap = " (16-20)";
                            elseif($r['room_name'] == "LECTURE ROOM 1") $cap = " (16-20)";
                            elseif($r['room_name'] == "LECTURE ROOM 2") $cap = " (16-20)";
                            elseif($r['room_name'] == "MARS") $cap = " (PERSONAL 1-2)";
                            elseif($r['room_name'] == "PLUTO") $cap = " (PERSONAL 1-2)";
                        ?>
                            <option value="<?= $r['id'] ?>"><?= $r['room_name'] . $cap ?></option>
                        <?php endforeach; ?>
                        </select>
                </div>
                <div class="form-group"><label>Tarikh</label><input type="date" name="booking_date" id="edit_date" required></div>
            </div>
            <div class="form-row">
                <div class="form-group"><label>Masa Mula</label><input type="time" name="start_time" id="edit_start" required></div>
                <div class="form-group"><label>Masa Tamat</label><input type="time" name="end_time" id="edit_end" required></div>
            </div>
            <div class="form-group">
                <label>Tujuan</label>
                <select name="purpose" id="edit_purpose" onchange="toggleForm('modalEdit')" required>
                    <option value="PCC">PCC</option><option value="OG">OG</option><option value="PO">PO</option><option value="CENTER">CENTER</option><option value="MEETING/LAIN-LAIN">MEETING</option>
                </select>
            </div>
            <div id="class_fields_modalEdit">
                <div class="form-row">
                    <div class="form-group"><label>Subjek</label><input type="text" name="subject" id="edit_subject" placeholder="Subjek"></div>
                    <div class="form-group"><label>Tingkatan/Darjah</label><input type="text" name="student_group" id="edit_group" placeholder="Tingkatan"></div>
                </div>
                <div class="form-group"><label>Jenis Pakej</label><input type="text" name="package_name" id="edit_package" placeholder="Nama Pakej"></div>
            </div>
            <div id="remarks_fields_modalEdit" style="display:none;">
                <div class="form-group"><label>Catatan</label><textarea name="remarks" id="edit_remarks" rows="2" placeholder="Catatan..."></textarea></div>
            </div>
            <div class="modal-footer"><button type="button" class="btn-secondary" onclick="closeModal('modalEdit')">Batal</button><button type="submit" class="btn-save">Simpan Perubahan</button></div>
        </form>
    </div>
</div>

<footer style="margin-top: 40px; padding: 20px; text-align: center; color: #94a3b8; font-size: 12px; width: 100%;">
    <p>&copy; <?= date('Y') ?> <strong>COPYRIGHT BY ALEEYA FTA</strong>. All Rights Reserved.</p>
</footer>

<script>
    lucide.createIcons();
    function openModal(id) { document.getElementById(id).style.display = 'flex'; }
    function closeModal(id) { document.getElementById(id).style.display = 'none'; }
    function filterBookings() {
        const input = document.getElementById('bookingSearch').value.toLowerCase();
        document.querySelectorAll('.booking-card').forEach(card => card.style.display = card.innerText.toLowerCase().includes(input) ? 'flex' : 'none');
    }
    function filterStatus(status, btn) {
    // Tukar butang aktif
    document.querySelectorAll('.pill').forEach(p => p.classList.remove('active')); 
    btn.classList.add('active');

    document.querySelectorAll('.booking-card').forEach(card => {
        const isPast = card.classList.contains('past-booking');
        const isOngoing = card.classList.contains('is-ongoing-card');

        if (status === 'all') {
            card.style.display = 'flex';
        } 
        else if (status === 'ongoing') {
            // Tunjukkan hanya yang tengah berlangsung
            card.style.display = isOngoing ? 'flex' : 'none';
        } 
        else if (status === 'standby') {
            // Tunjukkan yang belum mula (bukan past DAN bukan ongoing)
            card.style.display = (!isPast && !isOngoing) ? 'flex' : 'none';
        }
    });
}
    function openEditModal(d) {
        document.getElementById('edit_id').value = d.id; document.getElementById('edit_day').value = d.booking_day;
        document.getElementById('edit_teacher').value = d.teacher_name; document.getElementById('edit_room').value = d.room_id;
        document.getElementById('edit_date').value = d.booking_date; document.getElementById('edit_start').value = d.start_time;
        document.getElementById('edit_end').value = d.end_time; document.getElementById('edit_purpose').value = d.purpose;
        document.getElementById('edit_subject').value = d.subject || ''; document.getElementById('edit_group').value = d.student_group || '';
        document.getElementById('edit_package').value = d.package_name || ''; document.getElementById('edit_remarks').value = d.remarks || '';
        toggleForm('modalEdit'); openModal('modalEdit');
    }
    function toggleForm(mId) {
        const modal = document.getElementById(mId), p = modal.querySelector('select[name="purpose"]').value, isM = (p === 'MEETING/LAIN-LAIN');
        document.getElementById('class_fields_' + mId).style.display = isM ? 'none' : 'block';
        document.getElementById('remarks_fields_' + mId).style.display = isM ? 'block' : 'none';
    }
    function confirmDelete(id) {
    // Ambil tarikh yang user tengah tengok sekarang dari URL
    const urlParams = new URLSearchParams(window.location.search);
    const currentDate = urlParams.get('date') || '<?= $today ?>';

    Swal.fire({
        title: 'Padam Tempahan?',
        text: "Tindakan ini tidak boleh dibatalkan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Padam!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Hantar sekali parameter date ke delete.php
            window.location.href = 'delete.php?id=' + id + '&date=' + currentDate;
        }
    })
}
    setInterval(function() {
        const urlParams = new URLSearchParams(window.location.search);
        const dateParam = urlParams.get('date');
        const todayStr = "<?php echo $today; ?>";
        
        if (!dateParam || dateParam === todayStr) {
            location.reload();
        }
    }, 120000); // 120,000ms = 2 minit
    // 1. Simpan kedudukan scroll bila user buat apa-apa tindakan (click/submit)
window.addEventListener('beforeunload', () => {
    localStorage.setItem('scrollPosition', window.scrollY);
});

// 2. Bila page dah refresh, automatik lompat balik ke tempat tadi
window.addEventListener('load', () => {
    const scrollPos = localStorage.getItem('scrollPosition');
    if (scrollPos) {
        window.scrollTo(0, parseInt(scrollPos));
        localStorage.removeItem('scrollPosition'); // Padam memori lepas guna
    }
});
function startClock() {
    const el = document.getElementById('live-clock');
    if(!el) return;
    
    setInterval(() => {
        const now = new Date();
        el.textContent = now.toLocaleTimeString('en-GB', { 
            hour12: false, 
            hour: '2-digit', 
            minute: '2-digit', 
            second: '2-digit' 
        });
    }, 1000);
}
document.addEventListener('DOMContentLoaded', startClock);
</script>
</body>
</html>