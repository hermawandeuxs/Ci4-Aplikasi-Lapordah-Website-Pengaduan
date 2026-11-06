<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; font-size: 11pt; }
        .title { text-align: center; font-weight: bold; text-transform: uppercase; }
        table { border-collapse: collapse; width: 100%; margin-top: 10px; }
        td, th { word-break: break-word; vertical-align: top; }
        th, td { border: 1px solid #000; padding: 4px; }
        th { text-align: center; }
        .footer { margin-top: 40px; width: 100%; }
        .footer .ttd { text-align: right; }
    </style>
</head>
<body>
    <div class="title">
        LAPORAN PENGELOLAAN PENGADUAN PELAYANAN PUBLIK<br>
        DINAS LINGKUNGAN HIDUP KOTA PEKALONGAN<br>
        TAHUN <?= date('Y') ?>
    </div>
    <table>
        <tr>
            <th style="width:6%;">No</th>
            <th style="width:15%;">Tanggal Aduan</th>
            <th style="width:15%;">Nama Pelapor</th>
            <th style="width:12%;">Alamat Pelapor</th>
            <th style="width:15%;">Kanal Aduan</th>
            <th style="width:15%;">Penerima Aduan</th>
            <th style="width:19%;">Unit Pelayanan Yang Dituju</th>
            <th style="width:15%;">Isi Aduan</th>
            <th style="width:17%;">Tindak Lanjut Penyelesaian Aduan</th>
            <th style="width:15%;">Tanggal Tindak Lanjut</th>
            <th style="width:12%;">Status</th>
            <th style="width:15%;">Keterangan</th>
        </tr>
        <tr>
            <th>(1)</th>
            <th>(2)</th>
            <th>(3)</th>
            <th>(4)</th>
            <th>(5)</th>
            <th>(6)</th>
            <th>(7)</th>
            <th>(8)</th>
            <th>(9)</th>
            <th>(10)</th>
            <th>(11)</th>
            <th>(12)</th>
        </tr>
        <?php foreach ($reports as $i => $r): ?>
            <?php
                // Ambil hanya note dari status 'completed'
                $notes = [];
                $last_log_date = '';
                if (isset($r['id'])) {
                    $logs = model('App\Models\ReportStatusLogModel')
                        ->where('report_id', $r['id'])
                        ->where('status', 'completed')
                        ->orderBy('updated_at', 'ASC')
                        ->findAll();
                    foreach ($logs as $log) {
                        if (!empty($log['note'])) {
                            $notes[] = $log['note'];
                        }
                        $last_log_date = $log['updated_at']; // Akan berisi tanggal log terakhir 'completed'
                    }
                }
                $all_notes = $notes ? implode("<br><hr>", array_map('esc', $notes)) : '-';
            ?>
            <tr>
                <td style="text-align:center;"><?= $i+1 ?></td>
                <td><?= isset($r['created_at']) ? date('d/m/Y', strtotime($r['created_at'])) : '-' ?></td>
                <td><?= esc($r['nama_pelapor'] ?? '-') ?></td>
                <td><?= esc($r['alamat_pelapor'] ?? '-') ?></td>
                <td>Aplikasi LaporDAH</td>
        <?php
        // Ambil nama user dari log status 'completed' jika nama_user kosong
        $penerimaAduan = $r['nama_user'] ?? '';
        if (empty($penerimaAduan) && isset($r['id'])) {
            $log = model('App\Models\ReportStatusLogModel')
                ->where('report_id', $r['id'])
                ->where('status', 'completed')
                ->orderBy('updated_at', 'DESC')
                ->first();
            if ($log && !empty($log['user_id'])) {
                $user = model('App\Models\UserModel')->find($log['user_id']);
                if ($user && !empty($user['nama_user'])) {
                    $penerimaAduan = esc($user['nama_user']);
                }
            }
        }
        if (empty($penerimaAduan)) $penerimaAduan = '-';
    ?>
                <td><?= $penerimaAduan ?></td>
                <?php
                    // Ambil kebidangan dari report_status_log untuk status 'completed' oleh admin bidang
                    $kebidangan = '-';
                    if (isset($r['id'])) {
                        $log = model('App\Models\ReportStatusLogModel')
                            ->where('report_id', $r['id'])
                            ->where('status', 'completed')
                            ->orderBy('updated_at', 'DESC')
                            ->first();
                        if ($log && !empty($log['kebidangan'])) {
                            $kebidangan = esc($log['kebidangan']);
                        }
                    }
                ?>
                <td><?= $kebidangan ?></td>
                <td><?= esc($r['chronology'] ?? '-') ?></td>
                <td><?= $all_notes ?></td>
                <td><?= $last_log_date ? date('d/m/Y', strtotime($last_log_date)) : '-' ?></td>
                <td>
                    <?php
                        // Jika pada note report ada kata 'completed', tampilkan 'Selesai', jika tidak tampilkan status asli
                        $status_note = isset($r['note']) ? strtolower($r['note']) : '';
                        if (strpos($status_note, 'completed') !== false) {
                            echo 'Selesai';
                        } else {
                            echo esc($r['status'] ?? '-');
                        }
                    ?>
                </td>
                <td><?= esc($r['keterangan'] ?? '.') ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
    <div class="footer">
        <div class="ttd" style="margin-top: 60px;">
            Pekalongan, <?= date('d F Y') ?><br>
            Kepala Dinas Lingkungan Hidup<br>
            Kota Pekalongan<br>
            <br><br><br><br> <!-- Tambah jarak ekstra untuk ruang tanda tangan -->
            <b>Dr. SRI BUDI SANTOSO, M.Si</b><br>
            Pembina Utama Muda<br>
            NIP. 19710214 199903 1 004
        </div>
    </div>
</body>
</html>