<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita Acara Rekapitulasi Hasil Pemilihan Ketua OSIS</title>
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Times+New+Roman&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="icon" href="{{ asset('/img/logosss.png') }}" type="image/x-icon">

    <style>
        body {
            background-color: #f1f5f9;
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            padding: 24px;
        }

        .paper {
            background: #ffffff;
            max-width: 820px;
            margin: 0 auto;
            padding: 48px 56px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            border-radius: 4px;
            border: 1px solid #e2e8f0;
        }

        .kop-surat {
            border-bottom: 3px double #000000;
            padding-bottom: 14px;
            margin-bottom: 24px;
            text-align: center;
            position: relative;
        }

        .kop-logo {
            position: absolute;
            left: 10px;
            top: 5px;
            width: 75px;
        }

        .kop-surat h4 {
            font-size: 16px;
            font-weight: bold;
            margin: 0;
            letter-spacing: 0.5px;
        }

        .kop-surat h3 {
            font-size: 19px;
            font-weight: bold;
            margin: 3px 0;
            letter-spacing: 0.5px;
        }

        .kop-surat p {
            font-size: 13px;
            margin: 0;
            color: #333;
        }

        .judul-dokumen {
            text-align: center;
            margin-bottom: 24px;
        }

        .judul-dokumen h4 {
            font-size: 16px;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 4px;
        }

        .judul-dokumen p {
            font-size: 13px;
            margin: 0;
        }

        .table-laporan {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
            font-size: 13.5px;
        }

        .table-laporan th,
        .table-laporan td {
            border: 1px solid #000000;
            padding: 8px 12px;
        }

        .table-laporan th {
            background-color: #f1f5f9;
            text-align: center;
            font-weight: bold;
        }

        .signature-section {
            margin-top: 36px;
            font-size: 13.5px;
        }

        .sig-block {
            text-align: center;
            width: 48%;
            display: inline-block;
            vertical-align: top;
            margin-bottom: 30px;
        }

        .sig-space {
            height: 70px;
        }

        .sig-name {
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 2px;
        }

        .no-print-bar {
            max-width: 820px;
            margin: 0 auto 20px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #ffffff;
            padding: 12px 24px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.06);
            font-family: 'Poppins', sans-serif;
        }

        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }

            .no-print-bar {
                display: none !important;
            }

            .paper {
                box-shadow: none;
                border: none;
                padding: 0;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="no-print-bar">
    <div>
        <a href="/dashboard" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Dashboard
        </a>
    </div>
    <div class="d-flex align-items-center">
        <span class="text-muted mr-3" style="font-size: 13px;">Format siap cetak / simpan PDF</span>
        <button onclick="window.print()" class="btn btn-success btn-sm font-weight-bold px-3">
            <i class="fas fa-print mr-1"></i> Cetak / Simpan PDF
        </button>
    </div>
</div>

<div class="paper">
    <!-- Kop Surat -->
    <div class="kop-surat">
        <img src="/img/logoss.png" class="kop-logo" alt="Logo">
        <h4>ORGANISASI SISWA INTRA SEKOLAH (OSIS)</h4>
        <h3>PANITIA PEMILIHAN KETUA & WAKIL KETUA OSIS</h3>
        <p>Aplikasi E-Voting Pilketos Resmi • Masa Bakti 2026/2027</p>
    </div>

    <!-- Judul Dokumen -->
    <div class="judul-dokumen">
        <h4>BERITA ACARA REKAPITULASI HASIL PENGHITUNGAN SUARA</h4>
        <p>Nomor: 001/PAN-PILKETOS/BA/{{ date('Y') }}</p>
    </div>

    <!-- Pernyataan Pembuka -->
    <p style="font-size: 13.5px; line-height: 1.6; text-align: justify; margin-bottom: 18px;">
        Pada hari ini, <strong>{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</strong>, telah dilaksanakan pemungutan dan penghitungan suara Pemilihan Ketua dan Wakil Ketua OSIS secara digital melalui Sistem E-Voting Pilketos dengan hasil rekapitulasi sebagai berikut:
    </p>

    <!-- Tabel Rekap Paslon -->
    <table class="table-laporan">
        <thead>
            <tr>
                <th style="width: 10%;">No. Urut</th>
                <th style="width: 50%;">Nama Calon Ketua & Wakil Ketua</th>
                <th style="width: 20%;">Perolehan Suara</th>
                <th style="width: 20%;">Persentase (%)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($hasilVote as $hv)
            <tr>
                <td style="text-align: center; font-weight: bold;">{{ $hv['no_urut_paslon'] }}</td>
                <td>
                    <strong>{{ $hv['ketua_paslon'] }}</strong>
                    @if(!empty($hv['wakil_paslon']))
                        <br><span style="font-size: 12px; color: #444;">Wakil: {{ $hv['wakil_paslon'] }}</span>
                    @endif
                </td>
                <td style="text-align: center; font-weight: bold;">{{ $hv['jumlah_vote'] }} Suara</td>
                <td style="text-align: center; font-weight: bold;">{{ $hv['percentage'] }}%</td>
            </tr>
            @endforeach
            <tr style="background-color: #fafafa; font-weight: bold;">
                <td colspan="2" style="text-align: right; padding-right: 14px;">Total Suara Sah:</td>
                <td style="text-align: center;">{{ $totalSuara }} Suara</td>
                <td style="text-align: center;">{{ $totalSuara > 0 ? '100%' : '0%' }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Tabel Statistik Kehadiran -->
    <p style="font-weight: bold; margin-bottom: 6px; font-size: 13.5px;">Statistik Partisipasi Pemilih:</p>
    <table class="table-laporan" style="margin-bottom: 24px;">
        <tbody>
            <tr>
                <td style="width: 60%;">1. Total Daftar Pemilih Tetap (DPT Siswa)</td>
                <td style="font-weight: bold; width: 40%; text-align: right;">{{ $totalSiswa }} Siswa</td>
            </tr>
            <tr>
                <td>2. Jumlah Pemilih yang Menggunakan Hak Suara (Suara Sah)</td>
                <td style="font-weight: bold; text-align: right;">{{ $totalSuara }} Siswa ({{ $persentasePartisipasi }}%)</td>
            </tr>
            <tr>
                <td>3. Jumlah Pemilih yang Tidak Menggunakan Hak Suara (Golput)</td>
                <td style="font-weight: bold; text-align: right;">{{ $golput }} Siswa ({{ $persentaseGolput }}%)</td>
            </tr>
        </tbody>
    </table>

    <!-- Pernyataan Penutup -->
    <p style="font-size: 13.5px; line-height: 1.6; text-align: justify; margin-bottom: 30px;">
        Demikian Berita Acara Rekapitulasi Hasil Penghitungan Suara ini dibuat dengan sebenarnya dan ditandatangani oleh Panitia Pelaksana dan Saksi-saksi Pasangan Calon untuk dapat dipergunakan sebagaimana mestinya.
    </p>

    <!-- Tanda Tangan -->
    <div class="signature-section">
        <div class="sig-block">
            <p>Saksi Pasangan Calon 1,</p>
            <div class="sig-space"></div>
            <p class="sig-name">( .................................................. )</p>
        </div>
        <div class="sig-block">
            <p>Saksi Pasangan Calon 2,</p>
            <div class="sig-space"></div>
            <p class="sig-name">( .................................................. )</p>
        </div>

        <div class="sig-block" style="margin-top: 10px;">
            <p>Ketua Panitia Pelaksana,</p>
            <div class="sig-space"></div>
            <p class="sig-name">( .................................................. )</p>
            <p style="font-size: 12px; margin: 0;">NIS. ........................................</p>
        </div>
        <div class="sig-block" style="margin-top: 10px;">
            <p>Mengetahui,<br>Pembina OSIS,</p>
            <div class="sig-space"></div>
            <p class="sig-name">( .................................................. )</p>
            <p style="font-size: 12px; margin: 0;">NIP. ........................................</p>
        </div>
    </div>
</div>

</body>
</html>
