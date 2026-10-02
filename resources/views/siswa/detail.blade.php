@php
    use App\Voting;
    use App\HasilVoting;
@endphp

@extends('layouts.app')

<style>
.fixed-buttons {
    position: fixed; /* Mengatur posisi tetap */
    bottom: 20px; /* Jarak dari bawah halaman */
    left: 50%; /* Mengatur dari kiri */
    transform: translateX(-50%); /* Menggeser ke kiri agar terpusat */
    z-index: 1000; /* Mengatur lapisan agar tombol berada di atas elemen lain */
}

.button-wrapper {
    background-color: white; /* Warna latar belakang putih */
    padding: 10px 20px; /* Padding untuk ruang di dalam tombol */
    border-radius: 5px; /* Memberikan sudut yang membulat */
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2); /* Memberikan efek bayangan */
    display: flex; /* Mengatur tombol dalam baris */
    gap: 10px; /* Memberikan jarak antara tombol */
}

.fixed-buttons .btn {
    padding: 12px 20px; /* Mengubah padding untuk memperbesar ukuran tombol */
    font-size: 16px; /* Mengubah ukuran font tombol */
    border-radius: 8px; /* Menambah sudut yang membulat untuk penampilan lebih besar */
    margin-bottom: 0; /* Menghilangkan margin bawah */
}


</style>

@section('title')
Detail Paslon
@endsection

@section('css')
<link rel="stylesheet" href="/css/detail.css">
@endsection

@section('content')

<section class="bg-primary mt-n4">
    <div class="container">
        <div class="row judul mx-auto">
            <div class="col-md-5 mt-5">
                <h1 class="text-white">Detail Calon Ketua</h1>
            </div>
        </div>
        <div class="row d-flex justify-content-between mx-auto">
            <div class="col-md-6">
                <div class="card mx-auto">
                    <div class="card-body d-flex flex-column justify-content-center mt-4">
                        <img src="/img_ketua/{{ $data->img_ketua }}"
                            style="width: 200px; height: 200px; object-fit: cover; border-radius: 50%;"
                            class="mx-auto mt-n4" loading="lazy">

                        <div class="nama mt-4">
                            <h3 class="text-center">{{ $data->ketua_paslon }}</h3>
                            <p class="text-center mt-n1">Ketua</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mx-auto">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <div class="nama mt-4">
                            <h3 class="text-center mt-n1">Nomor Urut {{ $data->no_urut_paslon }}</h3>
                            <h2 class="text-center mt-n1">Calon Ketua OSIS</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content">
            <div class="row mx-auto rowVisi">
                <div class="col-md-6">
                    <div class="judul visi">
                        <h1 style="margin-left: 16px;">Visi Kandidat</h1>
                    </div>
                    <div class="isiVisi ml-4">
                        <p>{{ $data->visi_paslon }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="judul misi">
                        <h1 style="margin-left: 16px;"> Misi Kandidat</h1>
                    </div>
                    <div class="isiMisi ml-4">
                        @php
                        $misi = explode("\r\n", $data->misi_paslon);
                        @endphp
                        @foreach ($misi as $m)

                        <p>{{ $m }}</p>

                        @endforeach

                    </div>
                </div>
            </div>
            <div class="row mx-auto rowMisi">
            </div>
        </div>
        <div class="fixed-buttons">
            <div class="button-wrapper">
                <a href="/home" class="btn btn-danger btnKembali mr-1">Kembali</a>
                @php
                    $id_user = Auth::user()->id;
                @endphp

                @if(Voting::where('id_user', $id_user)->first())
                    <a href="/pilihPaslon/{{ $data->id }}" class="btn btn-success btn-mb shadow disabled voteBtn">Pilih Calon</a> <!-- Ukuran tombol kecil -->
                @else
                    <a href="/pilihPaslon/{{ $data->id }}"
                    class="btn btn-success btn-mb shadow voteBtn {{ (count(HasilVoting::all()) >= 1) ? 'disabled' : '' }}"
                    onclick="confirmVote(event)">Pilih Calon</a> <!-- Panggil fungsi konfirmasi -->
                @endif
            </div>
        </div>
    </div>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
</section>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Fungsi untuk menampilkan konfirmasi
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

    // Fungsi untuk memproses voting
    function handleVote(url, voteButton) {
        // Nonaktifkan tombol dan ubah teks pada tombol yang dipilih
        voteButton.classList.add('disabled'); // Tambahkan kelas disabled hanya pada tombol yang ditekan
        voteButton.innerText = 'Menyimpan...'; // Ubah teks tombol

        // AJAX request untuk voting jika diperlukan
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


@endsection
