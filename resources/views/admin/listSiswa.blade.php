@extends('layouts.app')

@section('title')
List Siswa
@endsection

@section('css')
<link rel="stylesheet" href="/css/dashboard.css">
@endsection

@section('content')

<section class="bg-primary mt-n4">
    <div class="container">
        <div class="row">
            @include('sweetalert::alert')
            <div class="col-md-12 mt-5">
                <div class="header">
                    <h1 class="text-white">List Siswa</h1>
                </div>
                <div class="card mt-3">
                    <div class="card-body mx-auto" style="width: 95%">
                        <div class="col-md-12 p-0">
                            <a href="/dashboard" class="btn btn-outline-secondary mt-2 mb-3 btnpaslon">Dashboard</a>
                                <div class="dropdown float-right mt-2 mb-4">
                                    <button class="btn btnpaslon btn-success dropdown-toggle" type="button"
                                        id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false">
                                        Register Siswa
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                        <a href="/user/importSiswa" class="dropdown-item"><i class="fas fa-file-import text-success mr-2"></i> Import Excel</a>
                                        <a href="{{ route('user.exportExcel') }}" class="dropdown-item"><i class="fas fa-file-export text-primary mr-2"></i> Export Siswa</a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="/registerSiswa"><i class="fas fa-user-plus text-info mr-2"></i> Manual Register</a>
                                    </div>
                                </div>
                                <form method="GET" action="{{ route('listSiswa') }}" class="form-inline mb-3" id="searchForm">
                                    <!-- Search Box -->
                                    <div class="form-group">
                                        <input type="text" name="search" id="searchInput" class="form-control full-width" placeholder="Cari Siswa..." value="{{ request('search') }}">
                                    </div>

                                    <!-- Dropdown untuk memilih status voting -->
                                    <div class="form-group ml-2">
                                        <select name="status" id="statusSelect" class="form-control">
                                            <option value="">Semua Status</option>
                                            <option value="voted" {{ request('status') == 'voted' ? 'selected' : '' }}>Sudah Memilih</option>
                                            <option value="not_voted" {{ request('status') == 'not_voted' ? 'selected' : '' }}>Belum Memilih</option>
                                        </select>
                                    </div>

                                    <!-- Dropdown untuk memilih kelas -->
                                    <div class="form-group ml-2">
                                        <select name="kelas" id="kelasSelect" class="form-control">
                                            <option value="">Semua Kelas</option>
                                            @if(isset($kelasList))
                                                @foreach($kelasList as $k)
                                                    <option value="{{ $k }}" {{ request('kelas') == $k ? 'selected' : '' }}>{{ $k }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>

                                    <!-- Dropdown untuk memilih jumlah data per halaman -->
                                    <div class="form-group ml-2">
                                        <select name="perPage" id="perPageSelect" class="form-control">
                                            <option value="5" {{ request('perPage') == 5 ? 'selected' : '' }}>5</option>
                                            <option value="10" {{ request('perPage') == 10 ? 'selected' : '' }}>10</option>
                                            <option value="20" {{ request('perPage') == 20 ? 'selected' : '' }}>20</option>
                                            <option value="30" {{ request('perPage') == 30 ? 'selected' : '' }}>30</option>
                                            <option value="50" {{ request('perPage') == 50 ? 'selected' : '' }}>50</option>
                                        </select>
                                    </div>
                                </form>


                                <!-- Tabel Data Siswa -->
                                <div id="siswaTable">
                                    @include('admin.partials._listSiswaTable') <!-- Partial view yang dimuat lewat AJAX -->
                                </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Fungsi untuk mengambil data dan memperbarui tabel
        function updateTable(pageUrl) {
            let url = pageUrl || '{{ route("listSiswa") }}';
            $.ajax({
                url: url,
                type: 'GET',
                data: {
                    search: $('#searchInput').val(),
                    status: $('#statusSelect').val(),
                    kelas: $('#kelasSelect').val(),
                    perPage: $('#perPageSelect').val()
                },
                success: function(data) {
                    $('#siswaTable').html(data);
                }
            });
        }

        // Event listener saat input pencarian diketik
        let searchTimer;
        $('#searchInput').on('keyup', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(function() {
                updateTable();
            }, 300);
        });

        // Event listener saat filter dropdown berubah
        $('#statusSelect, #kelasSelect, #perPageSelect').on('change', function() {
            updateTable();
        });

        // Event delegation untuk pagination links via AJAX
        $(document).on('click', '#siswaTable .pagination a', function(e) {
            e.preventDefault();
            let pageUrl = $(this).attr('href');
            if (pageUrl) {
                updateTable(pageUrl);
            }
        });
    });
</script>

@endsection
