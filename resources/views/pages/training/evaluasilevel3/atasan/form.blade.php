@extends('layout.main')

@section('content')
<div class="container">
    <h4 class="mb-4">Form Evaluasi Level 3 - Atasan</h4>

    {{-- Pelatihan & Peserta Info --}}
    <div class="card mb-4">
        <div class="card-body">
            <strong>Kode Pelatihan:</strong> {{ $evaluasiPeserta->pelatihan->kode_pelatihan }} <br>
            <strong>Judul Pelatihan:</strong> {{ $evaluasiPeserta->pelatihan->judul }} <br>
            <strong>Penyelenggara:</strong> {{ $evaluasiPeserta->pelatihan->penyelenggara }} <br>
            <strong>Peserta:</strong> {{ $evaluasiPeserta->user->name }} ({{ $evaluasiPeserta->user->registration_id }}) <br>
            <strong>Jabatan Saat Pelatihan:</strong> {{ $evaluasiPeserta->atasanEvaluation->participantSnapshot->jabatan_full ?? '-' }} <br>
            <strong>Department Saat Pelatihan:</strong> {{ $evaluasiPeserta->atasanEvaluation->participantSnapshot->department->name ?? '-' }} <br>
            <strong>Atasan:</strong> {{ optional($evaluasiPeserta->atasanEvaluation->atasan)->name ?? '-' }}
        </div>
    </div>

    <form action="{{ route('evaluasi-level-3.atasan.store', $evaluasiPeserta->atasanEvaluation->id) }}" method="POST">
        @csrf

        {{-- Tujuan Pembelajaran Section --}}
        <h4 class="mb-3">Tujuan Pembelajaran</h4>
        <div class="mb-3">
            <small>
                <strong>Keterangan:</strong><br>
                *) Silakan isi apakah tujuan pembelajaran dari pelatihan ini tercapai pada peserta.<br>
                **) Frekuensi: 0 (tidak pernah), 1 (sekali), 2 (sering), 3 (selalu)<br>
                ***) Hasil: 1 (tidak berhasil), 2 (cukup berhasil), 3 (berhasil), 4 (sangat berhasil)
            </small>
        </div>
        <table class="table table-bordered" id="tujuan-table">
            <thead>
                <tr>
                    <th>Tujuan Pembelajaran</th>
                    <th>Tercapai?</th>
                    <th>Frekuensi</th>
                    <th>Hasil</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($evaluasiPeserta->atasanEvaluation->tujuanPembelajarans as $i => $tujuan)
                <tr data-row-index="{{ $i }}">
                    <td>
                        <input type="text" name="tujuan[{{ $i }}]" class="form-control" required
                               value="{{ old('tujuan.'.$i, $tujuan->tujuan_pembelajaran) }}">
                    </td>
                    <td class="text-center">
                        <select name="tercapai[{{ $i }}]" class="form-select">
                            <option value="ya" @if(old('tercapai.'.$i, $tujuan->diaplikasikan)) selected @endif>Ya</option>
                            <option value="tidak" @if(!old('tercapai.'.$i, $tujuan->diaplikasikan)) selected @endif>Tidak</option>
                        </select>
                    </td>
                    <td>
                        <input type="number" name="frekuensi[{{ $i }}]" class="form-control" min="0" max="3"
                               value="{{ old('frekuensi.'.$i, $tujuan->frekuensi ?? 0) }}" required>
                    </td>
                    <td>
                        <input type="number" name="hasil[{{ $i }}]" class="form-control" min="1" max="4"
                               value="{{ old('hasil.'.$i, $tujuan->hasil ?? 1) }}" required>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-sm remove-row">Hapus</button>
                    </td>
                </tr>
                @empty
                <tr data-row-index="0">
                    <td><input type="text" name="tujuan[0]" class="form-control" required></td>
                    <td class="text-center">
                        <select name="tercapai[0]" class="form-select">
                            <option value="ya">Ya</option>
                            <option value="tidak">Tidak</option>
                        </select>
                    </td>
                    <td><input type="number" name="frekuensi[0]" class="form-control" min="0" max="3" value="0" required></td>
                    <td><input type="number" name="hasil[0]" class="form-control" min="1" max="4" value="1" required></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-sm remove-row">Hapus</button>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <button type="button" class="btn btn-primary btn-sm mb-3" id="add-tujuan">Tambah Baris</button>

        {{-- Feedback Section --}}
        <h4 class="mb-3">Feedback Atasan</h4>
        <h5 class="mb-3">Apa yang bisa dilakukan oleh HC Department / Atasan untuk membantu peserta mengaplikasikan hasil training</h5>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Peserta Telah Mampu</th>
                    <th>Kondisi kerja di divisi tidak mendukung karena...</th>
                    <th>Memberikan informasi mengenai...</th>
                    <th>Lain-lain</th>
                </tr>
            </thead>
            <tbody>
                @php $feedback = $evaluasiPeserta->atasanEvaluation->feedbacks->first(); @endphp
                <tr>
                    <td class="text-center">
                        <input type="checkbox" name="telah_mampu" value="1"
                            {{ old('telah_mampu', $feedback->telah_mampu ?? 0) ? 'checked' : '' }}>
                    </td>
                    <td><input type="text" name="tidak_diaplikasikan_karena" class="form-control"
                               value="{{ old('tidak_diaplikasikan_karena', $feedback->tidak_diaplikasikan_karena ?? '') }}"></td>
                    <td><input type="text" name="memberikan_informasi_mengenai" class="form-control"
                               value="{{ old('memberikan_informasi_mengenai', $feedback->memberikan_informasi_mengenai ?? '') }}"></td>
                    <td><input type="text" name="lain_lain" class="form-control"
                               value="{{ old('lain_lain', $feedback->lain_lain ?? '') }}"></td>
                </tr>
            </tbody>
        </table>

        {{-- General Section --}}
        <h4 class="mb-3">Penilaian Umum</h4>
        <div class="mb-3">
            <label class="form-label">Setelah mengikuti training, bawahan staff saya mampu untuk</label>
            <textarea name="manfaat_pelatihan" class="form-control" rows="3">{{ old('manfaat_pelatihan', $evaluasiPeserta->atasanEvaluation->manfaat_pelatihan ?? '') }}</textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Kinerja peserta setelah pelatihan</label>
            <input type="number" name="kinerja" class="form-control" min="1" max="5"
                   value="{{ old('kinerja', $evaluasiPeserta->atasanEvaluation->kinerja ?? '') }}">
            <small class="form-text text-muted">
                1 = tidak sama sekali, 2 = kurang meningkat, 3 = cukup meningkat, 5 = sangat meningkat
            </small>
        </div>
        <div class="mb-3">
            <label class="form-label">Saran / Masukan untuk peserta</label>
            <textarea name="saran" class="form-control" rows="3">{{ old('saran', $evaluasiPeserta->atasanEvaluation->saran ?? '') }}</textarea>
        </div>

        {{-- Submit Buttons --}}
        <div class="mb-3">
            <button type="submit" class="btn btn-success">Submit</button>
            <a href="{{ route('evaluasi-level-3.atasan.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let rowCounter = {{ $evaluasiPeserta->atasanEvaluation->tujuanPembelajarans->count() ?? 1 }};
    const addButton = document.getElementById('add-tujuan');
    const tableBody = document.querySelector('#tujuan-table tbody');

    addButton.addEventListener('click', function(e) {
        e.preventDefault();
        const newRow = `
            <tr data-row-index="${rowCounter}">
                <td><input type="text" name="tujuan[${rowCounter}]" class="form-control" required></td>
                <td class="text-center">
                    <select name="tercapai[${rowCounter}]" class="form-select">
                        <option value="ya">Ya</option>
                        <option value="tidak">Tidak</option>
                    </select>
                </td>
                <td><input type="number" name="frekuensi[${rowCounter}]" class="form-control" min="0" max="3" value="0" required></td>
                <td><input type="number" name="hasil[${rowCounter}]" class="form-control" min="1" max="4" value="1" required></td>
                <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-row">Hapus</button></td>
            </tr>`;
        tableBody.insertAdjacentHTML('beforeend', newRow);
        rowCounter++;
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-row')) {
            if (tableBody.rows.length > 1) e.target.closest('tr').remove();
            else alert('Minimal harus ada satu baris Tujuan Pembelajaran');
        }
    });
});
</script>
@endsection
