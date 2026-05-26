<?php
// ============================================
// api.php — BAHAGIAN BACKEND
// ============================================

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// ── DEBUG: Log semua request ──────────────────────────────────────────────
$method = $_SERVER['REQUEST_METHOD'];
$query = $_GET;
$action = $query['action'] ?? 'NO_ACTION';
$input = file_get_contents('php://input');
error_log("=== API REQUEST ===");
error_log("METHOD: $method");
error_log("ACTION: '$action'");
error_log("QUERY: " . json_encode($query));
error_log("BODY: $input");

require_once 'config.php';

// ── OPTIONS CORS ──────────────────────────────────────────────────────────
if ($method === 'OPTIONS') {
    http_response_code(200);
    exit('OK');
}

// ── GET routes ────────────────────────────────────────────────────────────
if ($method === 'GET') {
    if ($action === 'get_all') {
        $filter = $query['filter'] ?? 'harian';
        $today = date('Y-m-d');
        $where = match($filter) {
            'harian' => "WHERE DATE(tarikh) = '$today'",
            'mingguan' => "WHERE YEARWEEK(tarikh, 1) = YEARWEEK('$today', 1)",
            'bulanan' => "WHERE YEAR(tarikh) = YEAR('$today') AND MONTH(tarikh) = MONTH('$today')",
            default => ''
        };
        $sql = "SELECT * FROM tempahan $where ORDER BY tarikh DESC, masa_mula ASC";
        $result = $conn->query($sql);
        $data = [];
        while ($row = $result->fetch_assoc()) $data[] = $row;
        echo json_encode(['status' => 'ok', 'data' => $data]);
        exit;
    }

    if ($action === 'stats') {
        $today = date('Y-m-d');
        $total = $conn->query("SELECT COUNT(*) c FROM tempahan")->fetch_assoc()['c'];
        $hari_ini = $conn->query("SELECT COUNT(*) c FROM tempahan WHERE tarikh='$today'")->fetch_assoc()['c'];
        $minggu = $conn->query("SELECT COUNT(*) c FROM tempahan WHERE YEARWEEK(tarikh,1)=YEARWEEK('$today',1)")->fetch_assoc()['c'];
        echo json_encode(['status'=>'ok','total'=>(int)$total,'hari_ini'=>(int)$hari_ini,'minggu'=>(int)$minggu]);
        exit;
    }

    // ── SLOT STATUS ──────────────────────────────────────────────────────
    if ($action === 'slot_status') {
        $today = date('Y-m-d');
        $max = 5;

        // Slot A: 6:00 petang - 7:30 malam (18:00 - 19:30)
        //  tambah AND device = 'projector' — tablet & ipad tidak kira dalam had slot
        $slotA = $conn->query("SELECT COUNT(*) c FROM tempahan WHERE tarikh='$today' AND masa_mula >= '18:00' AND masa_mula < '19:30' AND device = 'projector'")->fetch_assoc()['c'];

        // Slot B: 8:00 malam - 10:00 malam (20:00 - 22:00)
        // tambah AND device = 'projector'
        $slotB = $conn->query("SELECT COUNT(*) c FROM tempahan WHERE tarikh='$today' AND masa_mula >= '20:00' AND masa_mula <= '22:00' AND device = 'projector'")->fetch_assoc()['c'];

        echo json_encode([
            'status' => 'ok',
            'slots' => [
                ['label' => '6:00 — 7:30 Malam', 'count' => (int)$slotA, 'max' => $max],
                ['label' => '8:00 — 10:00 Malam', 'count' => (int)$slotB, 'max' => $max],
            ]
        ]);
        exit;
    }
}

// ── POST routes ───────────────────────────────────────────────────────────
if ($method === 'POST') {
    $postData = json_decode($input, true);

    // ✅ DELETE ENDPOINT
    if ($action === 'delete') {
        error_log("DELETE ENDPOINT HIT! ID: " . ($postData['id'] ?? 'NO_ID'));

        $id = (int)($postData['id'] ?? 0);
        if (!$id) {
            echo json_encode(['status'=>'error','message'=>'ID kosong']);
            exit;
        }

        $stmt = $conn->prepare("DELETE FROM tempahan WHERE id=?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $deleted = $stmt->affected_rows > 0;
            echo json_encode([
                'status' => $deleted ? 'ok' : 'not_found',
                'message' => $deleted ? "Berjaya padam ID $id" : "Tiada rekod ID $id",
                'deleted_rows' => $stmt->affected_rows
            ]);
        } else {
            echo json_encode(['status'=>'error','message'=>'Query error: '.$conn->error]);
        }
        $stmt->close();
        exit;
    }

    // ──  ENDPOINT ──────────────────────────────────────────────────
    if ($action === 'tambah') {
        $nama = trim($conn->real_escape_string($postData['nama'] ?? ''));
        $tarikh = trim($conn->real_escape_string($postData['tarikh'] ?? ''));
        $masa_mula = trim($conn->real_escape_string($postData['masa_mula'] ?? ''));
        $masa_tamat = trim($conn->real_escape_string($postData['masa_tamat'] ?? ''));
        $kelas = trim($conn->real_escape_string($postData['kelas'] ?? ''));
        $tujuan = trim($conn->real_escape_string($postData['tujuan'] ?? ''));
        //  baca device — fallback 'projector' jika tiada
        $device = trim($conn->real_escape_string($postData['device'] ?? 'projector'));

        // Pastikan nilai device sah sahaja (keselamatan)
        if (!in_array($device, ['projector', 'tablet', 'ipad'])) {
            $device = 'projector';
        }

        //  ubah semakan: kelas wajib projector sahaja, masa wajib semua device
        if (!$nama || !$tarikh || !$tujuan) {
            echo json_encode(['status'=>'error','message'=>'Medan kosong']);
            exit;
        }

        if ($device === 'projector' && (!$kelas || !$masa_mula || !$masa_tamat)) {
            echo json_encode(['status'=>'error','message'=>'Sila lengkapkan kelas dan masa untuk projector']);
            exit;
        }

        if (($device === 'tablet' || $device === 'ipad') && (!$masa_mula || !$masa_tamat)) {
            echo json_encode(['status'=>'error','message'=>'Sila isi masa mula dan masa tamat']);
            exit;
        }

        // ──🛑 LOGIK HAD SLOT MASA — PROJECTOR SAHAJA ──
        //  tablet & ipad langkau semakan had slot terus
        if ($device === 'projector') {
            $max_per_slot = 5;

            // Slot A: 6:00 petang - 7:30 malam (18:00 - 19:30)
            $is_slot_A = ($masa_mula >= '18:00' && $masa_mula < '19:30');
            // Slot B: 8:00 malam - 10:00 malam (20:00 - 22:00)
            $is_slot_B = ($masa_mula >= '20:00' && $masa_mula < '22:00');

            if ($is_slot_A) {
                $sql_check = "SELECT COUNT(*) AS c FROM tempahan 
                              WHERE tarikh = '$tarikh' 
                              AND device = 'projector'
                              AND masa_mula >= '18:00' AND masa_mula < '19:30'";
            } elseif ($is_slot_B) {
                $sql_check = "SELECT COUNT(*) AS c FROM tempahan 
                              WHERE tarikh = '$tarikh' 
                              AND device = 'projector'
                              AND masa_mula >= '20:00' AND masa_mula < '22:00'";
            } else {
                $max_per_slot = 5;
                $sql_check = "SELECT COUNT(*) AS c FROM tempahan 
                              WHERE tarikh = '$tarikh' 
                              AND device = 'projector'
                              AND masa_mula < '$masa_tamat' 
                              AND masa_tamat > '$masa_mula'";
            }

            $res_check = $conn->query($sql_check);
            $row_check = $res_check->fetch_assoc();
            $count = (int)$row_check['c'];

            if ($count >= $max_per_slot) {
                echo json_encode([
                    'status' => 'penuh',
                    'message' => " Had: $max_per_slot tempahan ! (Sekarang ada: $count)."
                ]);
                exit;
            }
        }
        // ── TAMAT SEMAKAN — tablet & ipad terus ke INSERT ──

        // tambah kolum device dalam INSERT
        $sql = "INSERT INTO tempahan(nama,tarikh,masa_mula,masa_tamat,kelas,tujuan,device) VALUES('$nama','$tarikh','$masa_mula','$masa_tamat','$kelas','$tujuan','$device')";
        if ($conn->query($sql)) {
            echo json_encode(['status'=>'ok','message'=>'Berjaya tambah','id'=>$conn->insert_id]);
        } else {
            echo json_encode(['status'=>'error','message'=>$conn->error]);
        }
        exit;
    }
}


    if ($action === 'stats_device') {
        $today = date('Y-m-d');
        $proj   = $conn->query("SELECT COUNT(*) c FROM tempahan WHERE tarikh='$today' AND device='projector'")->fetch_assoc()['c'];
        $tablet = $conn->query("SELECT COUNT(*) c FROM tempahan WHERE tarikh='$today' AND device='tablet'")->fetch_assoc()['c'];
        $ipad   = $conn->query("SELECT COUNT(*) c FROM tempahan WHERE tarikh='$today' AND device='ipad'")->fetch_assoc()['c'];
        $total  = $conn->query("SELECT COUNT(*) c FROM tempahan WHERE tarikh='$today'")->fetch_assoc()['c'];
        echo json_encode([
            'status'    => 'ok',
            'projector' => (int)$proj,
            'tablet'    => (int)$tablet,
            'ipad'      => (int)$ipad,
            'total'     => (int)$total
        ]);
        exit;
    }


// ── 404 dengan debug info ─────────────────────────────────────────────────
http_response_code(404);
echo json_encode([
    'status' => 'error',
    'message' => 'Route tidak dijumpai',
    'debug' => [
        'method' => $method,
        'action' => $action,
        'query_string' => $_SERVER['QUERY_STRING'] ?? 'empty',
        'get_params' => $query,
        'post_data' => $postData ?? 'none',
        'full_url' => $_SERVER['REQUEST_URI']
    ]
]);
error_log("404 DEBUG: " . json_encode($_GET));
?>
