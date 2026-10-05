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
                <div class="card mt-3">
                    <div class="card-body mx-auto" style="width: 95%">
                        <div class="col-md-12 p-0">
                            <a href="/dashboard" class="btn btn-outline-secondary mt-2 btnpaslon">Dashboard</a>
                                <div class="dropdown float-right">
                                        <a href="/tambah" class="btn btn-success mt-2 btnpaslon">Tambah Calon</a>
                                </div>
                                <table class="table table-bordered mx-auto mt-3 table-striped table-responsive">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="15%">No Urut</th>
                                        <th class="text-center" width="55%">Nama Calon Ketua</th>
                                        <th class="text-center" width="30%">Aksi</th>
                                    </tr>
                                </thead>
                                @if( count($data) == 0 )
                                <tbody>
                                    <tr>
                                        <td colspan="6" align="center">Tidak Ada Caketos</td>
                                    </tr>
                                </tbody>
                                @else
                                <tbody>
                                    @foreach ($data as $d)
                                    <tr>
                                        <td class="text-center">{{ $d->no_urut_paslon }}</td>
                                        <td class="text-center">{{ $d->ketua_paslon }}</td>
                                        <td class="text-center">
                                            <a href="/edit/{{ $d->id }}" class="btn btn-primary btnaksi">Edit</a>
                                            <a href="/detailPaslon/{{ $d->id }}"
                                                class="btn btn-success btnDetail btnaksi">Detail</a>
                                            <a href="javascript:void(0);" class="btn btn-danger btnaksi" onclick="confirmDelete({{ $d->id }})">Hapus</a>
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>\
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
