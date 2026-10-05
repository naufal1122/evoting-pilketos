@extends('layouts.app')

@section('title')
Import Siswa
@endsection

@section('css')
<link rel="stylesheet" href="/css/importSiswa.css">
@endsection

@section('content')

@include('sweetalert::alert')
<section class="bg-primary mt-n4">
    <div class="container">
        <div class="row">
            <div class="col-md-12 mt-2">
                <div class="judul">
                    <h1 class="text-white mt-5">Import Data Pemilih (Siswa)</h1>
                </div>
                <div class="card mt-4 mb-5 mx-auto shadow-sm border-0">
                    <div class="card-body p-4">
                        <!-- Informasi Format Upload -->
                        <div class="alert alert-info border-0 shadow-sm mb-4" style="border-radius: 12px; background-color: #ecfdf5; color: #065f46;">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-info-circle fa-2x mr-3 mt-1" style="color: #10b981;"></i>
                                <div class="w-100">
                                    <h5 class="font-weight-bold mb-1" style="color: #065f46;">Panduan Format Upload File</h5>
                                    <p class="mb-2" style="font-size: 14px; line-height: 1.5;">
                                        File Excel/CSV hanya perlu memiliki 3 kolom utama:
                                        <strong>Nama Siswa</strong>, <strong>Kelas</strong>, dan <strong>NIS</strong> (Password).
                                        <br>
                                        <span class="text-dark"><strong>Username otomatis dibuat sistem</strong> dari 2 kata pertama nama siswa (contoh: <em>Dewa Naufal</em> &rarr; <em>dewanaufal</em>).</span>
                                    </p>
                                    <a href="{{ route('user.exportExcel') }}" class="btn btn-sm btn-success font-weight-bold px-3 py-2" style="border-radius: 8px;">
                                        <i class="fas fa-file-download mr-1"></i> Unduh Format / Template Excel
                                    </a>
                                </div>
                            </div>
                        </div>

                        <form action="/user/import_excel" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row mt-3 rowInput">
                                <div class="col-md-12">
                                    <label class="font-weight-bold" style="font-size: 14px;">Pilih File Excel / CSV (.xlsx, .xls, .csv)</label>
                                    <div class="input-group mb-3">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="fileExcel"
                                                aria-describedby="inputGroupFileAddon01" name="file_excel"
                                                accept=".xlsx,.xls,.csv"
                                                onchange="document.getElementById('filenameExcel').innerText = this.value.split('\\').pop();" required>
                                            <label class="custom-file-label" for="fileExcel">
                                                <p id="filenameExcel">Pilih file Excel / CSV</p>
                                            </label>
                                        </div>
                                    </div>
                                    @if($errors->has('file_excel'))
                                    <div class="text-danger mt-n2 mb-3">
                                        {{ $errors->first('file_excel') }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <div class="row mt-3 mb-2 rowInput">
                                <div class="col-md-12 d-flex justify-content-between">
                                    <a href="/listSiswa" class="btn btn-secondary px-4">
                                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                                    </a>
                                    <button type="submit" class="btn btn-primary px-4 font-weight-bold">
                                        <i class="fas fa-file-upload mr-1"></i> Mulai Import Data
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@section('js')
<script src="/js/preview.js"></script>
@endsection
