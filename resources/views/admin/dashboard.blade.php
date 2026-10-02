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
                <div class="card mt-2">
                    <div class="card-body mx-auto" style="width: 95%">
                        <div class="row">
                            <div class="header mt-3">
                                <h1 class="text" style="color: #394A5F; font-size: 28px;">Selamat datang di Aplikasi E - Pilketos</h1>
                                <p class="text" style="color: #929dab; font-size: 18px; margin-bottom: 4px;">Ini adalah halaman dashboard aplikasi e-voting pemilihan ketua OSIS di sekolah.</p>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="card-body mx-auto" style="width: 100%; background-color: #fcfdfe;">
                        <div class="row mb-2">
                            <div class="col-md-12 colBtn">
                                <a href="#"
                                class="btn btn-danger ml-1 btnSelesai btnpaslon float-right {{ ( count(HasilVoting::all()) >= 1 ) ? 'disabled' : '' }}"
                                id="voteSelesaiBtn">Vote Selesai</a>
                                <a href="/ulangVoting" class="btn btn-outline-danger ml-1 float-right btnUlang btnpaslon" id="ulangVotingBtn">Mulai Ulang Voting</a>

                                <!-- <a href="#"
                                class="btn btn-primary mr-1 float-left btnHasil btnpaslon"
                                id="statusBtn">Status: Voting Online</a> -->

                                <form action="{{ route('backup.database') }}" method="GET">
                                    <button type="submit" class="btn btn-outline-primary float-left ml-1 btnHasil btnpaslon">Backup Database</button>
                                </form>
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

</script>

@endsection
