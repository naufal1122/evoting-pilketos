@php
use App\Voting;
use App\HasilVoting;
@endphp

@extends('layouts.app')

<style>
    .responsive-img {
        width: 110px; /* Ukuran untuk desktop */
        height: 110px; /* Ukuran untuk desktop */
        object-fit: cover;
        border-radius: 50%;
        margin: 0 auto; /* Agar gambar terletak di tengah */
    }

    @media (max-width: 768px) { /* Gaya untuk tablet dan perangkat lebih kecil */
        .responsive-img {
            width: 100%; /* Ukuran untuk tablet */
            height: 100%; /* Ukuran untuk tablet */
            max-width: 180px; /* Sesuaikan maksimal untuk tablet */
            max-height: 180px; /* Sesuaikan maksimal untuk tablet */
        }
    }
</style>

@section('title')
Pemilihan
@endsection

@section('css')
<link rel="stylesheet" href="/css/homeSiswa.css">
@endsection

@section('content')
@include('sweetalert::alert')
<section class="mt-n4">
    <div class="container-fluid" style="padding-left: 20px; padding-right: 20px;">
        <div class="row judul mx-auto">
            <div class="col-md-12 mt-5 d-flex flex-wrap justify-content-between align-items-center">
                <div>
                    <h1 style="color: #1e293b; font-weight: 800; letter-spacing: -0.5px;">Pilih Caketos Kesayanganmu</h1>
                    <p class="text-muted mb-0" style="font-size: 14px;">Gunakan hak suaramu secara cerdas, jujur, dan adil untuk masa depan OSIS.</p>
                </div>
                @if( count(HasilVoting::all()) >= 1 )
                <a href="/hasilVote" class="neo-btn neo-btn-primary ml-1 mt-2">
                    <i class="fas fa-chart-pie mr-1"></i> Hasil Vote
                </a>
                @endif
            </div>
        </div>

        <!-- Banner Status Jadwal Pemilihan (Fitur 1) -->
        @if(isset($votingSchedule) && !$votingSchedule['is_buka'])
        <div class="row mx-auto mt-4">
            <div class="col-md-12">
                <div class="neo-card p-4 text-center" style="background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border-left: 5px solid #f59e0b;">
                    <div style="font-size: 32px; color: #d97706; margin-bottom: 8px;">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h4 class="font-weight-bold" style="color: #92400e;">Pemilihan Belum Dibuka / Telah Ditutup</h4>
                    <p class="mb-2" style="color: #78350f; font-size: 14.5px;">{{ $votingSchedule['pesan'] }}</p>
                    @if($votingSchedule['waktu_mulai'] && $votingSchedule['status'] === 'belum_mulai')
                        <div class="neo-inset d-inline-block py-2 px-4 mt-2 font-weight-bold" style="color: #92400e; font-size: 14px;">
                            <i class="fas fa-hourglass-start mr-1"></i> Waktu Buka: {{ \Carbon\Carbon::parse($votingSchedule['waktu_mulai'])->format('d M Y, H:i') }} WIB
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @elseif(isset($votingSchedule) && $votingSchedule['waktu_selesai'])
        <div class="row mx-auto mt-3">
            <div class="col-md-12">
                <div class="neo-card py-2 px-4 d-flex justify-content-between align-items-center" style="background: #ffffff;">
                    <div class="d-flex align-items-center">
                        <span class="badge badge-success mr-2 px-2 py-1"><i class="fas fa-check-circle mr-1"></i> Pemilihan Aktif</span>
                        <span class="text-muted" style="font-size: 13.5px;">Batas Waktu Pemilihan: <strong>{{ \Carbon\Carbon::parse($votingSchedule['waktu_selesai'])->format('d M Y, H:i') }} WIB</strong></span>
                    </div>
                    <span class="text-muted" style="font-size: 13px;">Auto-logout aktif setelah memilih (Kiosk Mode)</span>
                </div>
            </div>
        </div>
        @endif

        <div class="row mx-auto rowCard d-flex justify-content-center mt-4">
            @foreach($data as $d)
                <div class="col-md-4 mb-4">
                    <div class="card neo-card neo-card-hover mx-auto" style="width: 100%; min-height: 460px; display: flex; flex-direction: column; justify-content: space-between;">
                        <div class="card-header d-flex justify-content-between align-items-center" style="height: 56px; border-radius: 16px 16px 0 0; background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;">
                            <span class="font-weight-bold text-white text-uppercase" style="letter-spacing: 0.5px; font-size: 13px;">Calon Ketua OSIS</span>
                            <span class="badge badge-light font-weight-bold px-3 py-1" style="font-size: 13.5px; border-radius: 20px; color: #065f46;">No. Urut {{ $d->no_urut_paslon }}</span>
                        </div>
                        <div class="card-body d-flex flex-column justify-content-center text-center p-4">
                            <img src="/img_ketua/{{ $d->img_ketua }}" class="responsive-img mb-3" loading="lazy" alt="{{ $d->alt_text }}">

                            <div class="nama">
                                <h3 class="font-weight-bold mb-1" style="font-size: 20px; color: #1e293b;">{{ $d->ketua_paslon }}</h3>
                                <p class="text-muted mb-0" style="font-size: 13.5px;">Wakil: <strong>{{ !empty($d->wakil_paslon) ? $d->wakil_paslon : '-' }}</strong></p>
                            </div>
                        </div>

                        <div class="card-footer bg-transparent border-0 pt-0 pb-4 px-4 d-flex justify-content-center" style="gap: 10px;">
                            <a href="/detail/{{ $d->id }}" class="neo-btn neo-btn-secondary" style="font-size: 13.5px; padding: 8px 18px;">
                                <i class="fas fa-info-circle mr-1"></i> Detail
                            </a>
                            @php
                                $id_user = Auth::user()->id;
                                $alreadyVoted = Voting::where('id_user', $id_user)->first();
                                $votingTutup = (count(HasilVoting::all()) >= 1) || (isset($votingSchedule) && !$votingSchedule['is_buka']);
                            @endphp
                            @if($alreadyVoted)
                                <button class="neo-btn neo-btn-secondary disabled" style="font-size: 13.5px; padding: 8px 22px; opacity: 0.65; cursor: not-allowed;" disabled>
                                    <i class="fas fa-check-double mr-1 text-success"></i> Sudah Memilih
                                </button>
                            @elseif($votingTutup)
                                <button class="neo-btn neo-btn-secondary disabled" style="font-size: 13.5px; padding: 8px 22px; opacity: 0.65; cursor: not-allowed;" disabled>
                                    <i class="fas fa-lock mr-1"></i> Ditutup
                                </button>
                            @else
                                <a href="/pilihPaslon/{{ $d->id }}"
                                   class="neo-btn neo-btn-primary voteBtn"
                                   id="voteBtn"
                                   style="font-size: 13.5px; padding: 8px 22px;"
                                   onclick="confirmVote(event)">
                                    <i class="fas fa-check mr-1"></i> Pilih
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Fungsi untuk mengonfirmasi pilihan
    function confirmVote(event) {
        event.preventDefault(); // Mencegah tindakan default

        // Ambil URL untuk voting
        const url = event.currentTarget.getAttribute('href');
        const voteButton = event.currentTarget; // Tangkap tombol yang ditekan pengguna

        // Tampilkan konfirmasi SweetAlert sebelum melanjutkan
        Swal.fire({
            confirmButtonColor: '#3085d6',
            title: 'Konfirmasi Pilihan',
            text: 'Apakah Anda yakin ingin memilih calon ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, pilih!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Jika konfirmasi diterima, lanjutkan dengan menyimpan voting
                handleVote(url, voteButton);
            }
        });
    }

    // Fungsi untuk menyimpan voting
    function handleVote(url, voteButton) {
        // Nonaktifkan semua tombol vote untuk mencegah double click/vote
        document.querySelectorAll('#voteBtn, .voteBtn').forEach(btn => {
            btn.classList.add('disabled');
            btn.style.pointerEvents = 'none';
        });
        voteButton.innerText = 'Menyimpan...';

        // AJAX request untuk voting dengan header Json
        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(async response => {
            const data = await response.json().catch(() => null);
            if (response.ok && data && data.success) {
                let timerInterval;
                Swal.fire({
                    title: 'Pilihan Berhasil Dikirim!',
                    html: `
                        <p class="mb-2" style="font-size: 15px;">Terima kasih telah menggunakan hak suaramu.</p>
                        <div class="p-2 mb-2" style="background: #ecfdf5; border-radius: 8px; color: #065f46; font-size: 14px;">
                            <i class="fas fa-check-circle mr-1"></i> Suara Anda telah tercatat dengan aman.
                        </div>
                        <p class="text-muted mb-0" style="font-size: 13px;">
                            Sistem akan otomatis logout dalam <b><span id="kioskCountdown" style="color: #10b981; font-size: 18px;">3</span></b> detik...
                        </p>
                    `,
                    icon: 'success',
                    timer: 3000,
                    timerProgressBar: true,
                    showCancelButton: false,
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        const countdownEl = document.getElementById('kioskCountdown');
                        timerInterval = setInterval(() => {
                            const left = Math.ceil(Swal.getTimerLeft() / 1000);
                            if (countdownEl && left >= 0) {
                                countdownEl.textContent = left;
                            }
                        }, 200);
                    },
                    willClose: () => {
                        clearInterval(timerInterval);
                    }
                }).then(() => {
                    document.getElementById('logout-form').submit();
                });
            } else {
                const errMsg = (data && data.message) ? data.message : 'Terjadi kesalahan, silakan coba lagi.';
                Swal.fire({
                    title: 'Perhatian!',
                    text: errMsg,
                    icon: 'warning'
                }).then(() => {
                    window.location.reload();
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                title: 'Error!',
                text: 'Terjadi kesalahan sistem, silakan coba lagi.',
                icon: 'error'
            });
        });
    }
</script>



@if(session('success'))
    <script>
        Swal.fire({
            position: 'top-end', // Posisi di pojok kanan atas
            icon: 'success',
            title: '{{ session('success') }}',
            showConfirmButton: false,
            timer: 3000, // Menampilkan notifikasi selama 3 detik
            toast: true // Menampilkan notifikasi seperti toast
        });
    </script>
@endif

@if(session('warning'))
    <script>
        Swal.fire({
            position: 'top-end', // Posisi di pojok kanan atas
            icon: 'warning',
            title: '{{ session('warning') }}',
            showConfirmButton: false,
            timer: 3000, // Menampilkan notifikasi selama 3 detik
            toast: true // Menampilkan notifikasi seperti toast
        });
    </script>
@endif


@endsection



