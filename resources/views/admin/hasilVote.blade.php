@extends('layouts.app')
@section('title')
Hasil Vote
@endsection
@section('css')
<link rel="stylesheet" href="/css/hasilVote.css">
@endsection
@section('content')

<section class="mt-n4">
    <div class="container">
        <div class="row judul mx-auto">
            <div class="col-md-5 mt-5">
                <h1 class="text" style="color: #394A5F;">Hasil Vote</h1>
            </div>
        </div>
        <div class="row mx-auto rowCard d-flex justify-content-center mt-4">
            @foreach($data as $d)
            <div class="col-md-4 mb-4"> <!-- Ubah col-md-5 menjadi col-md-4 -->
                <div class="card shadow-lg border-light">
                    <div class="card-header bg-light text-center">
                        <h5 class="text-dark font-weight-bold">Paslon No {{ $d['no_urut_paslon'] }}</h5>
                    </div>
                    <div class="card-body d-flex justify-content-center align-items-center">
                        <h1 class="hasil text-success font-weight-bold display-2">{{ $d['jumlah_vote'] }}</h1>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="col-md-12 mt-2">
            <div class="card mt-2">
                <div class="card-body d-flex flex-column" style="width: 100%;">
                    <div class="row mt-4 flex-grow-1">
                        <div class="col-md-12">
                            <canvas id="votingChart" style="width: 100%; height: 100%;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <a href="{{ ( Auth::user()->role == 'admin' ) ? '/dashboard' : '/home' }}"
                   class="btn btn-danger mx-auto d-block mt-4 btnKembali rounded-pill">Kembali</a>
            </div>
        </div>
    </div>

</section>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        function updateVoteResults() {
            $.get('/user/hasil_vote_ajax', function(data) {
                data.forEach(function(item) {
                    $('#vote-result-' + item.no_urut_paslon).text(item.jumlah_vote);
                });
            });
        }

        // Update hasil setiap 5 detik
        setInterval(updateVoteResults, 5000);
    });

    // Ambil data dari PHP
    const data = @json($data);

    // Siapkan data untuk chart
    const labels = data.map(d => `Paslon No ${d.no_urut_paslon}`);
    const votes = data.map(d => d.jumlah_vote);

    // Membuat chart
    const ctx = document.getElementById('votingChart').getContext('2d');
    const votingChart = new Chart(ctx, {
        type: 'bar', // Tipe chart (bisa diganti dengan 'pie', 'line', dll)
        data: {
            labels: labels,
            datasets: [{
                label: 'Jumlah Suara',
                data: votes,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Jumlah Suara'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Paslon'
                    }
                }
            }
        }
    });

       // Fungsi untuk menampilkan notifikasi kecil SweetAlert di pojok kanan bawah
       function showSweetAlertCountdown() {
        var countdown = 10; // Mulai countdown dari 2 detik

        Swal.fire({
            position: 'top-end', // Posisi pojok kanan bawah
            icon: 'info',
            title: 'Update Hasil Vote',
            html: 'Halaman akan di-refresh dalam <strong>' + countdown + '</strong> detik.',
            timer: countdown * 1000,
            timerProgressBar: true,
            showConfirmButton: false,
            toast: true, // Menampilkan notifikasi seperti toast
            didOpen: () => {
                Swal.showLoading();
                var timerInterval = setInterval(() => {
                    countdown--;
                    Swal.getHtmlContainer().querySelector('strong').textContent = countdown;

                    // Jika countdown mencapai 0, refresh halaman
                    if (countdown <= 0) {
                        clearInterval(timerInterval);
                        window.location.reload(); // Refresh halaman
                    }
                }, 1000);
            },
            willClose: () => {
                clearInterval(timerInterval);
            }
        });
    }

    // Jalankan fungsi setelah halaman dimuat
    document.addEventListener("DOMContentLoaded", function() {
        showSweetAlertCountdown();
    });
</script>

@endsection
