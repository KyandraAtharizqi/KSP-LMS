@extends('layout.main')

@section('content')
<div class="container">
    <h4 class="mb-4">Form Evaluasi Level 3 - Atasan</h4>

    {{-- Pelatihan & Peserta Info --}}
    <div class="card mb-4">
        <div class="card-body">
            <strong>Kode:</strong> {{ $evaluasiPeserta->pelatihan->kode_pelatihan }} <br>
            <strong>Judul:</strong> {{ $evaluasiPeserta->pelatihan->judul }} <br>
            <strong>Penyelenggara:</strong> {{ $evaluasiPeserta->pelatihan->penyelenggara }} <br>
            <strong>Peserta:</strong> {{ $evaluasiPeserta->user->name }} ({{ $evaluasiPeserta->user->nik }}) <br>
            <strong>Unit Kerja:</strong> {{ $evaluasiPeserta->user->jabatan->name ?? '-' }}
        </div>
    </div>

    <form action="{{ route('evaluasi-level-3.atasan.store', $evaluasiPeserta->id) }}" method="POST">
        @csrf

        {{-- Tujuan Pembelajaran Section --}}
        <h4 class="mb-3">Tujuan Pembelajaran</h4>
        <div class="mb-3">
            <small>
                <strong>Keterangan:</strong><br>
                *) Silakan isi apakah tujuan pembelajaran dari pelatihan ini tercapai pada peserta.<br>
                **) Jika tidak, berikan catatan alasan / rekomendasi perbaikan.
            </small>
        </div>
        <table class="table table-bordered" id="tujuan-table">
            <thead>
                <tr>
                    <th>Tujuan Pembelajaran</th>
                    <th>Tercapai?</th>
                    <th>Catatan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr data-row-index="0">
                    <td><input type="text" name="tujuan[0]" class="form-control" required></td>
                    <td class="text-center">
                        <select name="tercapai[0]" class="form-select">
                            <option value="ya">Ya</option>
                            <option value="tidak">Tidak</option>
                        </select>
                    </td>
                    <td><textarea name="catatan[0]" class="form-control" rows="2"></textarea></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-sm remove-row">Hapus</button>
                    </td>
                </tr>
            </tbody>
        </table>
        <button type="button" class="btn btn-primary btn-sm mb-3" id="add-tujuan">Tambah Baris</button>

        {{-- Feedback Section similar to Peserta --}}
        <h4 class="mb-3">Feedback Atasan</h4>
        <h5 class="mb-3">Apa yang bisa dilakukan oleh HC Department / Atasan untuk membantu peserta mengaplikasikan hasil training</h5>
        <table class="table table-bordered" id="feedback-table">
            <thead>
                <tr>
                    <th>Peserta Telah Mampu</th>
                    <th>Kondisi kerja di divisi tidak mendukung karena...</th>
                    <th>Memberikan informasi mengenai...</th>
                    <th>Lain-lain</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center"><input type="checkbox" name="telah_mampu" value="1"></td>
                    <td><input type="text" name="tidak_diaplikasikan_karena" class="form-control"></td>
                    <td><input type="text" name="memberikan_informasi_mengenai" class="form-control"></td>
                    <td><input type="text" name="lain_lain" class="form-control"></td>
                </tr>
            </tbody>
        </table>

        {{-- General Section --}}
        <h4 class="mb-3">Penilaian Umum</h4>
        <div class="mb-3">
            <label class="form-label">Setelah mengikuti training, bawahan staff saya mampu untuk</label>
            <textarea name="manfaat_pelatihan" class="form-control" rows="3"></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Kinerja peserta setelah pelatihan</label>
            <input type="number" name="kinerja" class="form-control" min="1" max="5">
            <small class="form-text text-muted">
                1 = tidak sama sekali, 2 = kurang meningkat, 3 = cukup meningkat, 5 = sangat meningkat
            </small>
        </div>
        <div class="mb-3">
            <label class="form-label">Saran / Masukan untuk peserta</label>
            <textarea name="saran" class="form-control" rows="3"></textarea>
        </div>

    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let tujuanRowCounter = 1;
    const addButton = document.getElementById('add-tujuan');
    const tableBody = document.querySelector('#tujuan-table tbody');

    addButton.addEventListener('click', function(e) {
        e.preventDefault();
        const newRow = `
            <tr data-row-index="${tujuanRowCounter}">
                <td><input type="text" name="tujuan[${tujuanRowCounter}]" class="form-control" required></td>
                <td class="text-center">
                    <select name="tercapai[${tujuanRowCounter}]" class="form-select">
                        <option value="ya">Ya</option>
                        <option value="tidak">Tidak</option>
                    </select>
                </td>
                <td><textarea name="catatan[${tujuanRowCounter}]" class="form-control" rows="2"></textarea></td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm remove-row">Hapus</button>
                </td>
            </tr>
        `;
        tableBody.insertAdjacentHTML('beforeend', newRow);
        tujuanRowCounter++;
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-row')) {
            if (tableBody.rows.length > 1) {
                e.target.closest('tr').remove();
            } else {
                alert('Minimal harus ada satu baris Tujuan Pembelajaran');
            }
        }
    });
});
</script>
@endsection
