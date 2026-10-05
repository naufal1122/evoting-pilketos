@extends('layouts.app')

@section('title')
Register Siswa
@endsection

@section('css')
<link rel="stylesheet" href="/css/registerSiswa.css">
@endsection

@section('content')

@include('sweetalert::alert')
<section class="bg-primary mt-n4">
    <div class="container">
        <div class="row">
            <div class="col-md-12 mt-2">
                <div class="judul">
                    <h1 class="text-white mt-5">Register Siswa</h1>
                </div>
                <div class="card mt-4 mb-5">
                    <div class="card-body">
                        <form action="/prosesRegisterSiswa" method="POST">
                            @csrf
                            <div class="row mt-3 rowInput">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nama_panjang">Nama Lengkap</label>
                                        <input type="text" class="form-control" id="nama_panjang"
                                            placeholder="Masukkan Nama Lengkap Siswa" name="nama_panjang" value="{{ old('nama_panjang') }}" required autocomplete="nama_panjang" autofocus>

                                        @if( $errors->has('nama_panjang') )
                                        <div class="text-danger">
                                            {{ $errors->first('nama_panjang') }}
                                        </div>
                                        @endif

                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="username">Username <small class="text-muted">(Otomatis terisi dari nama)</small></label>
                                        <input type="text" class="form-control" id="username"
                                            placeholder="Otomatis dari 2 kata nama" name="username" value="{{ old('username') }}" autocomplete="username">

                                        @if( $errors->has('username') )
                                        <div class="text-danger">
                                            {{ $errors->first('username') }}
                                        </div>
                                        @endif

                                    </div>
                                </div>
                            </div>
                            <div class="row mt-2 rowInput">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="kelas">Kelas</label>
                                        <input type="text" class="form-control" id="kelas"
                                            placeholder="Masukkan Kelas Siswa (contoh: XII RPL 1)" name="kelas" value="{{ old('kelas') }}" required autocomplete="kelas">

                                        @if( $errors->has('kelas') )
                                        <div class="text-danger">
                                            {{ $errors->first('kelas') }}
                                        </div>
                                        @endif

                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password">NIS</label>
                                        <input type="text" class="form-control" id="password"
                                            placeholder="Masukkan NIS (digunakan untuk login siswa)" name="password" value="{{ old('password') }}" required autocomplete="password">

                                        @if( $errors->has('password') )
                                        <div class="text-danger">
                                            {{ $errors->first('password') }}
                                        </div>
                                        @endif

                                    </div>
                                </div>
                            </div>
                            <div class="row mt-4 mb-3 rowInput">
                                <div class="col-md-12">
                                    <a href="/listSiswa" class="btn btn-secondary mr-2">Kembali</a>
                                    <button type="submit" class="btn btn-primary m-auto">Register Siswa</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const namaInput = document.getElementById('nama_panjang');
    const usernameInput = document.getElementById('username');
    let userEditedUsername = false;

    usernameInput.addEventListener('input', function() {
        userEditedUsername = true;
    });

    namaInput.addEventListener('input', function() {
        if (!userEditedUsername) {
            const words = this.value.trim().split(/\s+/).filter(Boolean);
            if (words.length >= 2) {
                usernameInput.value = (words[0] + words[1]).toLowerCase().replace(/[^a-z0-9]/g, '');
            } else if (words.length === 1) {
                usernameInput.value = words[0].toLowerCase().replace(/[^a-z0-9]/g, '');
            } else {
                usernameInput.value = '';
            }
        }
    });
});
</script>

@endsection
