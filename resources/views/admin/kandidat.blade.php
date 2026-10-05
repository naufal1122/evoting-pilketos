@extends('layouts.app')

@section('title')
Kandidat
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
                    <h1 class="text-white">Kandidat</h1>
                </div>
                <div class="card mt-3 shadow-sm border-0">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <a href="/dashboard" class="btn btn-outline-secondary btnpaslon">
                                <i class="fas fa-arrow-left mr-1"></i> Dashboard
                            </a>
                            <a href="/tambah" class="btn btn-success btnpaslon">
                                <i class="fas fa-plus-circle mr-1"></i> Tambah Calon
                            </a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped mb-0" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 12%;">No Urut</th>
                                        <th class="text-center" style="width: 58%;">Nama Calon Ketua</th>
                                        <th class="text-center" style="width: 30%;">Aksi</th>
                                    </tr>
                                </thead>
                                @if( count($data) == 0 )
                                <tbody>
                                    <tr>
                                        <td colspan="3" align="center" class="py-4 text-muted">Tidak Ada Caketos</td>
                                    </tr>
                                </tbody>
                                @else
                                <tbody>
                                    @foreach ($data as $d)
                                    <tr>
                                        <td class="text-center font-weight-bold" style="font-size: 15px;">{{ $d->no_urut_paslon }}</td>
                                        <td class="text-center font-weight-bold" style="font-size: 15px; color: #1e293b;">{{ $d->ketua_paslon }}</td>
                                        <td class="text-center">
                                            <a href="/edit/{{ $d->id }}" class="btn btn-primary btnaksi">
                                                <i class="fas fa-edit mr-1"></i> Edit
                                            </a>
                                            <a href="/detailPaslon/{{ $d->id }}" class="btn btn-success btnDetail btnaksi">
                                                <i class="fas fa-eye mr-1"></i> Detail
                                            </a>
                                            <a href="javascript:void(0);" class="btn btn-danger btnaksi" onclick="confirmDelete({{ $d->id }})">
                                                <i class="fas fa-trash-alt mr-1"></i> Hapus
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmDelete(id) {
    Swal.fire({
        title: 'Yakin ingin dihapus?',
        text: 'Data ini akan dihapus dan tidak dapat dikembalikan!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Jika "Ya, hapus!" diklik, arahkan ke URL hapus
            window.location.href = '/hapus/' + id;
        }
    });
}
</script>

@endsection
