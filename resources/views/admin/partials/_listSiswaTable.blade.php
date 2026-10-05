<div class="table-responsive">
    <table class="table table-bordered table-striped mb-0" style="width: 100%;">
        <thead>
            <tr>
                <th class="text-center" style="width: 5%;">No</th>
                <th class="text-center" style="width: 18%;">Username</th>
                <th class="text-center" style="width: 25%;">Nama Panjang</th>
                <th class="text-center" style="width: 12%;">NIS</th>
                <th class="text-center" style="width: 12%;">Kelas</th>
                <th class="text-center" style="width: 16%;">Status Voting</th>
                <th class="text-center" style="width: 12%;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @if ($data->isEmpty())
            <tr>
                <td colspan="7" class="text-center py-4 text-muted">Tidak Ada Siswa</td>
            </tr>
            @else
            @foreach ($data as $index => $siswa)
            <tr>
                <td class="text-center">{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>
                <td class="text-center font-weight-bold" style="color: #047857;">{{ $siswa->username }}</td>
                <td class="text-center font-weight-bold" style="color: #1e293b;">{{ $siswa->nama_panjang }}</td>
                <td class="text-center">{{ $siswa->password }}</td>
                <td class="text-center">{{ $siswa->kelas }}</td>
                <td class="text-center">
                    @if ($siswa->voting)
                        <span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i> Sudah Memilih</span>
                    @else
                        <span class="badge badge-warning text-dark px-2 py-1"><i class="fas fa-clock mr-1"></i> Belum Memilih</span>
                    @endif
                </td>
                <td class="text-center">
                    <form id="deleteForm-{{ $siswa->id }}" action="{{ route('siswa.hapus', $siswa->id) }}" method="POST" style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                    <button type="button" class="btn btn-danger btnaksi" onclick="confirmDelete({{ $siswa->id }})"><i class="fas fa-trash-alt mr-1"></i> Hapus</button>
                </td>
            </tr>
            @endforeach
            @endif
        </tbody>
    </table>
</div>

<!-- Render pagination links -->
<div class="d-flex justify-content-center mt-3">
    {{ $data->links() }}
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmDelete(id) {
    Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: 'Data siswa akan dihapus dan tidak dapat dikembalikan!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Jika diklik "Ya, hapus!", kirimkan form
            document.getElementById('deleteForm-' + id).submit();
        } else if (result.isDismissed) {
            Swal.fire('Penghapusan dibatalkan!', '', 'info');
        }
    });
}
</script>
