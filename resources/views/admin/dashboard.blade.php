@php
use App\HasilVoting;
@endphp
@extends('layouts.app')

@section('title')
Dashboard
@endsection

@section('css')
<link rel="stylesheet" href="/css/dashboard.css">
@endsection

@section('content')

<section class="mt-n4">
    <div class="container">
        <div class="row">
            @include('sweetalert::alert')
            <div class="col-md-12 mt-4">
                <div class="header">
                    <h1 class="text mt-1" style="color: #929dab; font-size: 14px;">Halaman</h1>
                    <h1 class="text" style="color: #394A5F;">Dashboard</h1>
                </div>
                <div class="row mt-4">
                    <div class="col-md-4 mb-3">
                        <div class="card border-0 shadow-sm" style="height: 100%;">
                            <div class="card-body d-flex align-items-center">
                                <div style="width: 44px; height: 44px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; margin-right: 12px; box-shadow: 0 4px 10px rgba(16, 185, 129, 0.25);">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div>
                                    <h5 class="card-title ml-2" style="margin-bottom: 2px; margin-top: 0; font-weight: bold; color: #1e293b;"> {{ $totalSiswa }} Pemilih</h5>
                                    <p class="card-text ml-2 text-muted" style="margin: 0; font-size: 13.5px;">Total Pemilih</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card border-0 shadow-sm" style="height: 100%;">
                            <div class="card-body d-flex align-items-center">
                                <div style="width: 44px; height: 44px; background: linear-gradient(135deg, #059669 0%, #047857 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; margin-right: 12px; box-shadow: 0 4px 10px rgba(5, 150, 105, 0.25);">
                                    <i class="fas fa-user-tie"></i>
                                </div>
                                <div>
                                    <h5 class="card-title ml-2" style="margin-bottom: 2px; margin-top: 0; font-weight: bold; color: #1e293b;">{{ $totalPaslon }} Kandidat</h5>
                                    <p class="card-text ml-2 text-muted" style="margin: 0; font-size: 13.5px;">Total Kandidat</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card border-0 shadow-sm" style="height: 100%;">
                            <div class="card-body d-flex align-items-center" id="totalSuaraContainer">
                                <div style="width: 44px; height: 44px; background: linear-gradient(135deg, #14b8a6 0%, #0f766e 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; margin-right: 12px; box-shadow: 0 4px 10px rgba(20, 184, 166, 0.25);">
                                    <i class="fas fa-vote-yea"></i>
                                </div>
                                <div>
                                    <h5 class="card-title ml-2" style="margin-bottom: 2px; margin-top: 0; font-weight: bold; color: #1e293b;" id="totalSuara">{{ $totalSuara }} Suara</h5>
                                    <p class="card-text ml-2 text-muted" style="margin: 0; font-size: 13.5px;">Total Suara Masuk</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <div class="container">
        <div class="row">
            @include('sweetalert::alert')
            <div class="col-md-12 mt-2">
                <div class="card neo-card mt-2 mb-5">
                    <!-- Card Top Header: Welcome Banner & Tab Navigation Menu -->
                    <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                            <div class="mb-3 mb-md-0">
                                <h2 class="mb-1 font-weight-bold" style="color: #1e293b; font-size: 24px;">Selamat datang di E - Pilketos</h2>
                                <p class="text-muted mb-0" style="font-size: 14.5px;">Panel kontrol dan pusat manajemen pemilihan ketua OSIS sekolah.</p>
                            </div>
                            <!-- Menu Tab Bergantian (Scroll Kiri Kanan) -->
                            <div class="d-flex align-items-center">
                                <button type="button" class="neo-tab-arrow-btn mr-2" id="btnPrevDashboardTab" title="Sebelumnya">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <ul class="nav neo-nav-tabs" id="dashboardTabs" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="tab-aksi-link" data-toggle="tab" href="#pane-aksi" role="tab" aria-controls="pane-aksi" aria-selected="true">
                                            <i class="fas fa-th-large mr-1 text-success"></i> Menu Utama
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="tab-jadwal-link" data-toggle="tab" href="#pane-jadwal" role="tab" aria-controls="pane-jadwal" aria-selected="false">
                                            <i class="far fa-clock mr-1 text-info"></i> Status & Jadwal
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="tab-partisipasi-link" data-toggle="tab" href="#pane-partisipasi" role="tab" aria-controls="pane-partisipasi" aria-selected="false">
                                            <i class="fas fa-chart-pie mr-1 text-primary"></i> Partisipasi Kelas
                                        </a>
                                    </li>
                                </ul>
                                <button type="button" class="neo-tab-arrow-btn ml-2" id="btnNextDashboardTab" title="Selanjutnya">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <hr class="my-2" style="border-top: 1px solid rgba(0,0,0,0.06);">

                    <!-- Content Body Panes -->
                    <div class="card-body px-4 py-3">
                        <div class="tab-content" id="dashboardTabsContent">
                            <!-- TAB 1: Menu Utama / Tombol Aksi Cepat -->
                            <div class="tab-pane fade show active" id="pane-aksi" role="tabpanel" aria-labelledby="tab-aksi-link">
                                <div class="p-2">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between">
                                        <div class="d-flex flex-wrap gap-2 mb-2">
                                            <a href="{{ route('admin.liveCount') }}" target="_blank" class="neo-btn neo-btn-primary mr-2 mb-2">
                                                <i class="fas fa-desktop mr-1"></i> Layar Monitor Proyektor
                                            </a>
                                            <a href="{{ route('admin.beritaAcara') }}" target="_blank" class="neo-btn neo-btn-secondary mr-2 mb-2">
                                                <i class="fas fa-file-invoice mr-1"></i> Cetak Berita Acara
                                            </a>
                                            <form action="{{ route('backup.database') }}" method="GET" class="d-inline mb-2 mr-2">
                                                <button type="submit" class="neo-btn neo-btn-secondary">
                                                    <i class="fas fa-database mr-1"></i> Backup Database
                                                </button>
                                            </form>
                                        </div>
                                        <div class="d-flex flex-wrap gap-2 mb-2">
                                            <a href="/ulangVoting" class="neo-btn neo-btn-secondary text-danger mr-2 mb-2" id="ulangVotingBtn">
                                                <i class="fas fa-redo-alt mr-1"></i> Reset Voting
                                            </a>
                                            <a href="#"
                                               class="neo-btn neo-btn-danger mb-2 {{ ( count(HasilVoting::all()) >= 1 ) ? 'disabled' : '' }}"
                                               id="voteSelesaiBtn">
                                                <i class="fas fa-lock mr-1"></i> Kunci & Selesaikan
                                            </a>
                                        </div>
                                    </div>

                                    <div class="neo-inset p-3 mt-3" style="border-radius: 14px;">
                                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                                            <div>
                                                <span class="font-weight-bold text-muted" style="font-size: 13.5px;">Status Pelaksanaan Saat Ini:</span>
                                                <span class="ml-2 font-weight-bold" style="color: #1e293b;">
                                                    @if($votingSchedule['status'] === 'buka')
                                                        <span class="text-success"><i class="fas fa-circle mr-1" style="font-size: 9px;"></i> Pemilihan Buka (Aktif)</span>
                                                    @elseif($votingSchedule['status'] === 'belum_mulai')
                                                        <span class="text-warning"><i class="fas fa-hourglass-start mr-1"></i> Belum Dimulai</span>
                                                    @else
                                                        <span class="text-danger"><i class="fas fa-lock mr-1"></i> Pemilihan Ditutup</span>
                                                    @endif
                                                </span>
                                            </div>
                                            <div class="mt-2 mt-md-0">
                                                <button type="button" class="btn btn-sm btn-link text-success p-0 font-weight-bold" onclick="$('#tab-jadwal-link').tab('show')">
                                                    Atur Jadwal TPS <i class="fas fa-arrow-right ml-1"></i>
                                                </button>
                                                <span class="mx-2 text-muted">|</span>
                                                <button type="button" class="btn btn-sm btn-link text-primary p-0 font-weight-bold" onclick="$('#tab-partisipasi-link').tab('show')">
                                                    Lihat Partisipasi Kelas <i class="fas fa-arrow-right ml-1"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 2: Status & Jadwal Pemilihan -->
                            <div class="tab-pane fade" id="pane-jadwal" role="tabpanel" aria-labelledby="tab-jadwal-link">
                                <div class="p-2">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="font-weight-bold mb-0" style="color: #1e293b;">
                                            <i class="far fa-clock text-success mr-2"></i> Jadwal & Status Waktu Pemilihan
                                        </h5>
                                        <div>
                                            @if($votingSchedule['status'] === 'buka')
                                                <span class="neo-badge neo-badge-success">
                                                    <i class="fas fa-circle text-success mr-1" style="font-size: 8px;"></i> Pemilihan Buka (Aktif)
                                                </span>
                                            @elseif($votingSchedule['status'] === 'belum_mulai')
                                                <span class="neo-badge neo-badge-warning">
                                                    <i class="fas fa-hourglass-start text-warning mr-1"></i> Belum Dimulai
                                                </span>
                                            @else
                                                <span class="neo-badge neo-badge-danger">
                                                    <i class="fas fa-lock text-danger mr-1"></i> Pemilihan Ditutup
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-5 mb-3">
                                            <div class="neo-inset p-3" style="border-radius: 14px; height: 100%;">
                                                <h6 class="font-weight-bold text-muted mb-2">Informasi Jadwal Saat Ini</h6>
                                                <p class="mb-1" style="font-size: 14px;"><strong>Mode:</strong> <span class="text-capitalize">{{ $votingSchedule['mode'] }}</span></p>
                                                <p class="mb-1" style="font-size: 14px;"><strong>Waktu Buka:</strong> {{ $votingSchedule['waktu_mulai'] ? \Carbon\Carbon::parse($votingSchedule['waktu_mulai'])->format('d M Y, H:i') . ' WIB' : 'Belum diatur' }}</p>
                                                <p class="mb-2" style="font-size: 14px;"><strong>Waktu Tutup:</strong> {{ $votingSchedule['waktu_selesai'] ? \Carbon\Carbon::parse($votingSchedule['waktu_selesai'])->format('d M Y, H:i') . ' WIB' : 'Belum diatur' }}</p>
                                                <div class="alert alert-light mb-0 py-2 px-3 border" style="font-size: 13px; color: #475569;">
                                                    <i class="fas fa-info-circle text-info mr-1"></i> {{ $votingSchedule['pesan'] }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-7">
                                            <form action="{{ route('admin.updateJadwal') }}" method="POST">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-md-12 mb-3">
                                                        <label class="font-weight-bold" style="font-size: 13.5px;">Status Pemilihan</label>
                                                        <div class="d-flex flex-wrap gap-2">
                                                            <div class="custom-control custom-radio mr-3">
                                                                <input type="radio" id="modeOtomatis" name="status_pemilihan" value="otomatis" class="custom-control-input" {{ $votingSchedule['mode'] === 'otomatis' ? 'checked' : '' }}>
                                                                <label class="custom-control-label" for="modeOtomatis">Otomatis (Sesuai Jadwal Jam)</label>
                                                            </div>
                                                            <div class="custom-control custom-radio mr-3">
                                                                <input type="radio" id="modeBuka" name="status_pemilihan" value="buka" class="custom-control-input" {{ $votingSchedule['mode'] === 'buka' ? 'checked' : '' }}>
                                                                <label class="custom-control-label text-success font-weight-bold" for="modeBuka">Buka Manual</label>
                                                            </div>
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" id="modeTutup" name="status_pemilihan" value="tutup" class="custom-control-input" {{ $votingSchedule['mode'] === 'tutup' ? 'checked' : '' }}>
                                                                <label class="custom-control-label text-danger font-weight-bold" for="modeTutup">Tutup Manual</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="font-weight-bold" style="font-size: 13.5px;">Waktu Mulai Pemilihan</label>
                                                        <input type="datetime-local" name="waktu_mulai" class="form-control neo-input" value="{{ $votingSchedule['waktu_mulai'] ? \Carbon\Carbon::parse($votingSchedule['waktu_mulai'])->format('Y-m-d\TH:i') : '' }}">
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="font-weight-bold" style="font-size: 13.5px;">Waktu Selesai Pemilihan</label>
                                                        <input type="datetime-local" name="waktu_selesai" class="form-control neo-input" value="{{ $votingSchedule['waktu_selesai'] ? \Carbon\Carbon::parse($votingSchedule['waktu_selesai'])->format('Y-m-d\TH:i') : '' }}">
                                                    </div>
                                                    <div class="col-md-12 text-right">
                                                        <button type="submit" class="neo-btn neo-btn-primary">
                                                            <i class="fas fa-save mr-1"></i> Simpan Jadwal & Status
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 3: Statistik Partisipasi Kelas -->
                            <div class="tab-pane fade" id="pane-partisipasi" role="tabpanel" aria-labelledby="tab-partisipasi-link">
                                <div class="p-2">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="font-weight-bold mb-0" style="color: #1e293b;">
                                            <i class="fas fa-chart-pie text-primary mr-2"></i> Tingkat Partisipasi Pemilih per Kelas
                                        </h5>
                                        <span class="text-muted" style="font-size: 13px;">Total {{ count($kelasStats) }} Kelas Terdaftar</span>
                                    </div>
                                    <div class="table-responsive neo-inset p-2" style="border-radius: 14px;">
                                        <table class="table table-hover mb-0 bg-transparent" style="vertical-align: middle;">
                                            <thead>
                                                <tr>
                                                    <th style="padding-left: 20px;">Kelas</th>
                                                    <th class="text-center">Total DPT</th>
                                                    <th class="text-center">Sudah Memilih</th>
                                                    <th class="text-center">Belum Memilih</th>
                                                    <th style="width: 35%; padding-right: 20px;">Progress Partisipasi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($kelasStats as $ks)
                                                <tr>
                                                    <td style="padding-left: 20px; font-weight: 600; color: #1e293b;">
                                                        <i class="fas fa-graduation-cap text-muted mr-2"></i>{{ $ks['kelas'] }}
                                                    </td>
                                                    <td class="text-center">{{ $ks['total'] }}</td>
                                                    <td class="text-center font-weight-bold text-success">{{ $ks['voted'] }}</td>
                                                    <td class="text-center font-weight-bold text-muted">{{ $ks['unvoted'] }}</td>
                                                    <td style="padding-right: 20px;">
                                                        <div class="d-flex align-items-center">
                                                            <div class="neo-progress flex-grow-1 mr-2">
                                                                <div class="neo-progress-bar" style="width: {{ $ks['percentage'] }}%;"></div>
                                                            </div>
                                                            <span class="font-weight-bold" style="font-size: 13px; width: 48px; text-align: right; color: #065f46;">
                                                                {{ $ks['percentage'] }}%
                                                            </span>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted py-4">Belum ada data kelas siswa yang terdaftar.</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
        // SweetAlert untuk tombol "Vote Selesai"
        document.getElementById('voteSelesaiBtn').addEventListener('click', function(event) {
        if (this.classList.contains('disabled')) {
            event.preventDefault(); // Cegah tindakan jika tombol disabled
        } else {
            // Lanjutkan dengan konfirmasi
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Vote akan ditutup dan hasil tidak dapat diubah!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, selesaikan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '/voteSelesai'; // Redirect ke halaman voteSelesai
                }
            });
        }
    });


    // SweetAlert untuk tombol "Mulai Ulang Voting"
    document.getElementById('ulangVotingBtn').addEventListener('click', function(event) {
        event.preventDefault(); // Mencegah link berjalan otomatis
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Semua hasil voting akan direset dan tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, ulang voting!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Konfirmasi Terakhir',
                    text: "Anda benar-benar ingin menghapus semua hasil voting?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus sekarang!',
                    cancelButtonText: 'Batal'
                }).then((finalResult) => {
                    if (finalResult.isConfirmed) {
                        window.location.href = '/ulangVoting'; // Jalankan aksi setelah konfirmasi ganda
                    }
                });
            }
        });
    });

    function updateTotalSuara() {
        // Menambahkan loading animation
        $('#totalSuaraContainer').css('opacity', '0.5'); // Mengubah opacity menjadi lebih transparan

        $.ajax({
            url: "{{ route('get.total.suara') }}",
            method: 'GET',
            success: function(response) {
                // Memperbarui total suara
                $('#totalSuara').text(response.total + ' Suara');
                $('#totalSuaraContainer').css('opacity', '1'); // Mengembalikan opacity
            },
            error: function() {
                console.error('Error fetching total suara');
                $('#totalSuaraContainer').css('opacity', '1'); // Mengembalikan opacity
            }
        });
    }

    // Memperbarui total suara setiap 10 detik
    setInterval(updateTotalSuara, 10000);

    // Panggil fungsi update saat halaman dimuat
    $(document).ready(function() {
        updateTotalSuara();
    });

    // // Fungsi untuk menampilkan notifikasi kecil SweetAlert di pojok kanan bawah
    // function showSweetAlertCountdown() {
    //     var countdown = 5; // Mulai countdown dari 5 detik
    //     var interval = 10; // Interval pembaruan setiap 10 detik
    //     var timerInterval; // Untuk menyimpan interval timer

    //     // Menampilkan SweetAlert
    //     Swal.fire({
    //         position: 'top-end', // Posisi pojok kanan bawah
    //         icon: 'info',
    //         title: 'Update Hasil Vote',
    //         html: 'Halaman akan di-refresh dalam <strong>' + countdown + '</strong> detik.',
    //         timer: countdown * 1000,
    //         timerProgressBar: true,
    //         showConfirmButton: false,
    //         toast: true, // Menampilkan notifikasi seperti toast
    //         didOpen: () => {
    //             Swal.showLoading();
    //             timerInterval = setInterval(() => {
    //                 countdown--;
    //                 Swal.getHtmlContainer().querySelector('strong').textContent = countdown;

    //                 // Jika countdown mencapai 0, refresh halaman
    //                 if (countdown <= 0) {
    //                     clearInterval(timerInterval);
    //                     window.location.reload(); // Refresh halaman
    //                 }
    //             }, 1000);
    //         },
    //         willClose: () => {
    //             clearInterval(timerInterval);
    //         }
    //     });

    //     // Set interval untuk memicu notifikasi setiap 10 detik
    //     setInterval(() => {
    //         showSweetAlertCountdown(); // Memanggil fungsi untuk menampilkan SweetAlert lagi
    //     }, interval * 1000);
    // }

    // // Jalankan fungsi setelah halaman dimuat
    // document.addEventListener("DOMContentLoaded", function() {
    //     showSweetAlertCountdown();
    // });

    // Kontrol tombol scroll/ganti tab bergantian (Kiri - Kanan)
    const tabOrder = ['#tab-aksi-link', '#tab-jadwal-link', '#tab-partisipasi-link'];

    $('#btnPrevDashboardTab').on('click', function() {
        let activeIndex = tabOrder.findIndex(selector => $(selector).hasClass('active'));
        let prevIndex = (activeIndex - 1 + tabOrder.length) % tabOrder.length;
        $(tabOrder[prevIndex]).tab('show');
    });

    $('#btnNextDashboardTab').on('click', function() {
        let activeIndex = tabOrder.findIndex(selector => $(selector).hasClass('active'));
        let nextIndex = (activeIndex + 1) % tabOrder.length;
        $(tabOrder[nextIndex]).tab('show');
    });

</script>

@endsection
