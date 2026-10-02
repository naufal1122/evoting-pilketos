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
<section class="bg-primary mt-n4">
    <div class="container-fluid" style="padding-left: 15px; padding-right: 15px;">
        <div class="row judul mx-auto">
            <div class="col-md-7 mt-5">
                <h1 class="text-white">Pilih Caketos Kesayanganmu</h1>
                @if( count(HasilVoting::all()) >= 1 )
                <a href="/hasilVote" class="btn btn-success ml-1 mt-2 btnpaslon w-25">Hasil Vote</a>
                @endif
            </div>
        </div>
        <div class="row mx-auto rowCard d-flex justify-content-center mt-4">
            @foreach($data as $d)
                <div class="col-md-4"> <!-- Ubah dari col-md-12 menjadi col-md-4 -->
                    <div class="card mx-auto mb-4"> <!-- Tambahkan margin bawah untuk spasi antar card -->
                        <div class="card-header d-flex justify-content-center align-items-center">
                            <p>No Urut {{ $d->no_urut_paslon }}</p>
                        </div>
                        <div class="row mx-auto">
                            <div class="col-md-12 mt-4"> <!-- Ubah col-md-6 menjadi col-md-12 untuk satu kolom gambar -->
                                <div class="card-body d-flex flex-column justify-content-center">
                                    <img src="/img_ketua/{{ $d->img_ketua }}" class="responsive-img mt-n4" loading="lazy" alt="{{ $d->alt_text }}">

                                    <div class="nama mt-4">
                                        <h3 class="text-center" style="font-size: 22px;">{{ $d->ketua_paslon }}</h3>
                                        <p class="text-center mt-1">Ketua</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mx-auto rowOpsi">
                            <div class="col-md-12 d-flex justify-content-center">
                                <div class="opsi d-flex flex-row justify-content-center"> <!-- Gunakan flex-row untuk susunan horizontal -->
                                    <a href="/detail/{{ $d->id }}" class="btn btn-outline-success btn-mb mr-1">Detail</a> <!-- Ukuran tombol kecil -->
                                    @php
                                        $id_user = Auth::user()->id;
                                    @endphp
                                    @if(Voting::where('id_user', $id_user)->first())
                                        <a href="/pilihPaslon/{{ $d->id }}" class="btn btn-success btn-mb shadow disabled" id="voteBtn">Pilih</a> <!-- Ukuran tombol kecil -->
                                    @else
                                        <a href="/pilihPaslon/{{ $d->id }}"
                                        class="btn btn-success btn-mb shadow {{ (count(HasilVoting::all()) >= 1) ? 'disabled' : '' }}"
                                        id="voteBtn"
                                        onclick="confirmVote(event)">Pilih</a>
                                    @endif
                                </div>
                            </div>
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
        // Nonaktifkan tombol dan ubah teks
        voteButton.classList.add('disabled'); // Tambahkan kelas disabled ke tombol yang ditekan
        voteButton.innerText = 'Menyimpan...'; // Ubah teks tombol yang dipilih

        // AJAX request untuk voting
        fetch(url)
            .then(response => {
                if (response.ok) {
                    // Jika voting berhasil, tampilkan SweetAlert
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Terima kasih telah melakukan voting!',
                        icon: 'success',
                        showCancelButton: false,
                        showConfirmButton: false, // Menyembunyikan tombol konfirmasi
                    });

                    // Logout otomatis setelah 5 detik
                    setTimeout(() => {
                        document.getElementById('logout-form').submit(); // Logout secara otomatis
                    }, 5000);
                } else {
                    // Tangani error jika voting gagal
                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan, silakan coba lagi.',
                        icon: 'error'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'Terjadi kesalahan, silakan coba lagi.',
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



