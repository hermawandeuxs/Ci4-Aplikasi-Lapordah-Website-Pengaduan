<!-- filepath: app/Views/admin/rekap_laporan_pdf.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Rekap Laporan PDF</title>
    <style>
        body { font-family: Arial, sans-serif; color: #222; }
        h2, h3 { color: #23803c; margin-bottom: 8px; }
        h2 { border-bottom:2px solid #23803c; padding-bottom:8px; }
        .subtitle { font-size: 1em; color: #555; margin-bottom: 18px; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 0 !important; }
        th, td { border: 1px solid #333; padding: 8px; text-align: left; }
        th { background: #e0f7e9; }
        tr:nth-child(even) { background: #f7fafc; }
        .total-row { background: #e0f7e9; font-weight: bold; }
        .info-box {
            background: #e8f5e9;
            border-left: 5px solid #23803c;
            padding: 10px 16px;
            margin-bottom: 16px;
            font-size: 1em;
        }
        .update { color: #888; font-size: 0.95em; text-align: right; margin-bottom: 18px; }
        .section-title { margin-top: 32px; font-size: 1.1em; color: #23803c; }

        /* Tambahan CSS untuk menghindari pemotongan tabel saat dicetak */
        .rekap-bulanan-table, .rekap-bulanan-table thead, .rekap-bulanan-table tbody, .rekap-bulanan-table tfoot, .rekap-bulanan-table tr, .rekap-bulanan-table th, .rekap-bulanan-table td {
            page-break-inside: avoid !important;
        }
        .section-title,
        .rekap-bulanan-table {
            page-break-before: avoid !important;
            page-break-after: avoid !important;
        }
        .section-title {
            margin-bottom: 4px !important;
            padding-bottom: 0 !important;
        }
        .rekap-bulanan-table {
            margin-top: 0 !important;
        }

        /* Tambahan CSS untuk menghindari pemotongan tabel saat dicetak */
        .rekap-status-table, .rekap-bulanan-table thead, .rekap-bulanan-table tbody, .rekap-bulanan-table tfoot, .rekap-bulanan-table tr, .rekap-bulanan-table th, .rekap-bulanan-table td {
            page-break-inside: avoid !important;
        }

        .table-block {
            page-break-inside: avoid;
            page-break-before: auto;
            page-break-after: auto;
            margin-bottom: 18px;
        }
    </style>
</head>
<body>
    <h2>Rekap Laporan <?= $bulan ? "Bulan $bulan" : "Semua Bulan" ?> <?= $tahun ? "Tahun $tahun" : "Semua Tahun" ?></h2>
    <div class="subtitle">
        <b>Filter:</b>
        <?= $bulanDipilih ? $bulanList[$bulanDipilih] : 'Semua Bulan' ?>,
        <?= $tahunDipilih ? $tahunDipilih : 'Semua Tahun' ?>
        <br>
        <span class="update">Dicetak: <?= date('d-m-Y H:i') ?></span>
    </div>

    <?php if ($jumlahBulanLalu == 0): ?>
        <div class="info-box"><b>Tidak ada data bulan sebelumnya.</b></div>
    <?php endif; ?>

    <?php if (isset($persen) && $bulanDipilih && $tahunDipilih): ?>
        <div class="info-box">
            <b>Peningkatan dibanding bulan lalu:</b> <?= number_format($persen, 2) ?>%
        </div>
    <?php endif; ?>

    <div class="table-block">
        <!-- Tabel Rekap Kategori -->
        <h3 class="section-title">Rekap Jumlah Laporan per Kategori</h3>
        <table>
            <thead>
                <tr>
                    <th style="width:40px;">No</th>
                    <th>Kategori</th>
                    <th style="width:90px;">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <?php $no=1; foreach($rekap as $kategori => $jumlah): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= esc($kategori) ?></td>
                    <td><?= esc($jumlah) ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($rekap)): ?>
                <tr>
                    <td colspan="3" style="text-align:center;color:#888;">Tidak ada data rekap untuk filter ini.</td>
                </tr>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="2" style="text-align:right;">Total Semua Laporan:</td>
                    <td><?= array_sum($rekap) ?></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="table-block">
        <!-- Tabel Rekap Status -->
        <?php if (!empty($rekapStatus)): ?>
        <h3 class="section-title">Rekap Status Laporan</h3>
        <?php
            // Alias status
            $statusAlias = [
                'archived' => 'Diarsipkan',
                'disposition' => 'Disposisikan',
                'rejected' => 'Ditolak',
                'progress' => 'Ditindaklanjuti'
                // tambahkan alias lain jika perlu
            ];
        ?>
        <table class="rekap-status-table">
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($rekapStatus as $status => $jumlah): ?>
                <tr>
                    <td>
                        <?= isset($statusAlias[strtolower($status)]) ? $statusAlias[strtolower($status)] : ucfirst($status) ?>
                    </td>
                    <td><?= $jumlah ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

    <div class="table-block">
        <!-- Tabel Rekap Bulanan -->
        <?php if (!empty($rekapBulanan)): ?>
        <h3 class="section-title">Rekap Jumlah Laporan per Bulan <?= $tahun ? $tahun : '' ?></h3>
        <table class="rekap-bulanan-table">
            <thead>
                <tr>
                    <th>Bulan</th>
                    <th>Jumlah Laporan</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($rekapBulanan as $bulanNum => $jumlah): ?>
                <tr>
                    <td><?= $bulanList[$bulanNum] ?></td>
                    <td><?= $jumlah ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <?php
                    $totalBulanan = array_sum($rekapBulanan);
                    $desemberTerisi = isset($rekapBulanan[12]) && $rekapBulanan[12] > 0;
                    $labelTotal = $desemberTerisi ? 'Total :' : 'Total sementara :';
                ?>
                <tr class="total-row">
                    <td style="text-align:right;"><?= $labelTotal ?></td>
                    <td><?= $totalBulanan ?></td>
                </tr>
            </tfoot>
        </table>
        <?php endif; ?>
    </div>

</body>
</html>