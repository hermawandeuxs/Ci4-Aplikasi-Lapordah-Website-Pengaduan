<!-- filepath: app/Views/universal/print_template.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Formulir Penyelesaian Pengaduan</title>
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 12pt;
            margin: 50px;
        }
        .kop {
            text-align: center;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .kop strong {
            font-size: 14pt;
        }
        .title {
            text-align: center;
            font-weight: bold;
            text-decoration: underline;
            margin: 30px 0 10px;
            font-size: 13pt;
        }
        .section {
            margin: 15px 0;
        }
        .section strong {
            text-decoration: underline;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td {
            vertical-align: top;
            padding: 4px;
        }
        .checkbox-line {
            margin-top: 10px;
        }
        .checkbox-line label {
            display: inline-block;
            margin-right: 20px;
        }
        .ttd-container {
            margin-top: 40px;
            justify-content: space-between;
        }
        .ttd {
            width: 30%;
            text-align: center;
        }
        .space {
            height: 60px;
        }
        .underline {
            display: inline-block;
            border-bottom: 1px dotted #000;
            width: 300px;
        }
        @media print {
            body {
                margin: 10mm 10mm 10mm 10mm;
                font-size: 11pt;
            }
             .title, .section, .checkbox-line, .ttd-container {
                page-break-inside: avoid;
                break-inside: avoid;
            }
            table, tr, td {
                page-break-inside: avoid;
                break-inside: avoid;
            }

             @page {
                size: A4 portrait;
                margin: 10mm 10mm 10mm 10mm;
            }
       /* Tambahan: hilangkan tombol print saat print */
             .no-print {
                display: none !important;
            }

        }
    </style>
</head>
<body>


<div class="kop" style="text-align:center; margin-bottom:20px;">
    <img src="<?= esc($kop_surat) ?>" alt="Kop Surat" style="width:100%; max-width:700px;">
</div>

<div class="title">FORMULIR PENYELESAIAN PENGADUAN</div>

<p style="text-align:center;"><strong>Nomor: 600.1.4/____.___</strong> </p>

<div class="section">
    <strong>IDENTITAS PELAPOR</strong>
    <table>
        <tr><td width="25%">Nama</td><td>: <?= esc($report['nama_pelapor'] ?? '-') ?></td></tr>
        <tr><td>Alamat</td><td>: <?= esc($report['alamat_pelapor'] ?? '-') ?></td></tr>
        <tr><td>Nomor Aduan</td><td>: <?= esc( $report['id']) ?></td></tr>
        <tr><td>Tanggal Penerimaan</td><td>: <?= !empty($report['created_at']) ? date('j F Y', strtotime($report['created_at'])) : '-' ?></td></tr>
    </table>
</div>

<div class="section">
    <strong>RINGKASAN ADUAN</strong>
    <p><?= nl2br(esc($report['chronology'] ?? '-')) ?></p>
</div>


<div class="section">
    <strong>TINDAK LANJUT ADUAN</strong>
    <p><?= nl2br(esc($completed_note ?? '-')) ?></p>
</div>


<div class="section">
    <strong>STATUS ADUAN</strong>
    <div class="checkbox-line">
        <label><?= (strtolower($report['status']) === 'completed' ? '☑' : '☐') ?> Selesai</label>
        <label><?= (strtolower($report['status']) === 'rejected' ? '☑' : '☐') ?> Tidak dapat diselesaikan</label>
        <label><?= (strtolower($report['status']) === 'disposition' ? '☑' : '☐') ?> Disalurkan kepada UPP lain</label>
        <label><?= (!empty($report['status_lainnya']) ? '☑' : '☐') ?> Lainnya: <span class="underline"><?= esc($report['status_lainnya'] ?? '') ?>&nbsp;</span></label>
    </div>
</div>


<table style="width:100%; margin-top:40px; text-align:center;">
    <tr>
        <td>
            Pekalongan, <?= date('j F Y') ?><br>
            Mengetahui,
        </td>
        <td>
            &nbsp;
        </td>
        <td>
            &nbsp;
        </td>
    </tr>
    <tr>
        <td>Sebutan Kepala UPP</td>
        <td>Petugas Pengaduan</td>
        <td>Pejabat Yang Menangani</td>
    </tr>
    <tr>
        <td style="height:70px;"></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td>
            <u>(Dr. Sri Budi S, M.Si)</u>
        </td>
        <td>
            <u>(SOFIANA)</u>
        </td>
        <td>
            <u>(<?= esc($report['update_by'] ?? '...................................') ?>)</u>
        </td>
    </tr>
</table>

</body>
</html>