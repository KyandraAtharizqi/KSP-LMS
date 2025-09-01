@extends('layout.main')

@section('content')
<div class="container">
    <h4 class="mb-4">Edit Evaluasi Level 3 - Peserta</h4>

    {{-- Pelatihan Info --}}
    <div class="card mb-4">
        <div class="card-body">
            <strong>Kode:</strong> {{ $pelatihan->kode_pelatihan }} <br>
            <strong>Judul:</strong> {{ $pelatihan->judul }} <br>
            <strong>Penyelenggara:</strong> {{ $pelatihan->penyelenggara }}
        </div>
    </div>

    <form action="{{ route('evaluasi-level-3.peserta.update', [$pelatihan->id, $evaluasi->id]) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Action Plan Section --}}
        <h4 class="mb-3">Action Plan</h4>
        <div class="mb-3">
            <small>
                <strong>Keterangan:</strong><br>
                *) Action Plan: rencana langkah-langkah dalam mengaplikasikan materi pelatihan<br>
                **) Frekuensi: 0 (tidak pernah), 1 (sekali), 2 (sering), 3 (selalu)<br>
                ***) Hasil: 1 (tidak berhasil), 2 (cukup berhasil), 3 (berhasil), 4 (sangat berhasil)
            </small>
        </div>
        <table class="table table-bordered" id="action-plan-table">
            <thead>
                <tr>
                    <th>Action Plan</th>
                    <th>Diaplikasikan</th>
                    <th>Frekuensi (0-3)</th>
                    <th>Hasil (1-4)</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($evaluasi->actionPlans as $i => $plan)
                    <tr data-row-index="{{ $i }}">
                        <td>
                            <input type="text" 
                                   name="action_plan[{{ $i }}]" 
                                   class="form-control" 
                                   value="{{ old('action_plan.'.$i, $plan->action_plan) }}" 
                                   required>
                        </td>
                        <td class="text-center">
                            <input type="hidden" name="diaplikasikan[{{ $i }}]" value="0">
                            <input type="checkbox" name="diaplikasikan[{{ $i }}]" value="1"
                                   {{ old('diaplikasikan.'.$i, $plan->diaplikasikan) ? 'checked' : '' }}>
                        </td>
                        <td>
                            <input type="number" name="frekuensi[{{ $i }}]" class="form-control" 
                                   min="0" max="3" 
                                   value="{{ old('frekuensi.'.$i, $plan->frekuensi) }}" required>
                        </td>
                        <td>
                            <input type="number" name="hasil[{{ $i }}]" class="form-control" 
                                   min="1" max="4" 
                                   value="{{ old('hasil.'.$i, $plan->hasil) }}" required>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-danger btn-sm remove-row">Hapus</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <button type="button" class="btn btn-primary btn-sm mb-3" id="add-action-plan">Tambah Baris</button>

        {{-- Feedback Section --}}
        <h4 class="mb-3">Feedback</h4>
        <h5 class="mb-3">Apa yang bisa dilakukan oleh HC Department untuk membantu anda mengaplikasikan hasil training</h5>
        <table class="table table-bordered" id="feedback-table">
            <thead>
                <tr>
                    <th>Saya Sudah Mampu</th>
                    <th>Butuh Bantuan untuk Cara Mengaplikasikan</th>
                    <th>Materi Tidak Bisa Diaplikasikan Karena</th>
                    <th>Memberikan Informasi Mengenai</th>
                    <th>Lain-lain</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center">
                        <input type="checkbox" name="telah_mampu" value="1"
                               {{ old('telah_mampu', $evaluasi->feedbacks->first()->telah_mampu ?? 0) ? 'checked' : '' }}>
                    </td>
                    <td class="text-center">
                        <input type="checkbox" name="membantu_mengaplikasikan" value="1"
                               {{ old('membantu_mengaplikasikan', $evaluasi->feedbacks->first()->membantu_mengaplikasikan ?? 0) ? 'checked' : '' }}>
                    </td>
                    <td>
                        <input type="text" name="tidak_diaplikasikan_karena" class="form-control"
                               value="{{ old('tidak_diaplikasikan_karena', $evaluasi->feedbacks->first()->tidak_diaplikasikan_karena ?? '') }}">
                    </td>
                    <td>
                        <input type="text" name="memberikan_informasi_mengenai" class="form-control"
                               value="{{ old('memberikan_informasi_mengenai', $evaluasi->feedbacks->first()->memberikan_informasi_mengenai ?? '') }}">
                    </td>
                    <td>
                        <input type="text" name="lain_lain" class="form-control"
                               value="{{ old('lain_lain', $evaluasi->feedbacks->first()->lain_lain ?? '') }}">
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- General Section --}}
        <h4 class="mb-3">General</h4>
        <div class="mb-3">
            <label class="form-label">Setelah pelatihan ini saya mampu untuk...</label>
            <textarea name="manfaat_pelatihan" class="form-control" rows="3">{{ old('manfaat_pelatihan', $evaluasi->manfaat_pelatihan) }}</textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Secara keseluruhan, saya menilai training tersebut dapat meningkatkan kinerja...</label>
            <input type="number" name="kinerja" class="form-control" min="0" max="2"
                   value="{{ old('kinerja', $evaluasi->kinerja) }}">
            <small class="form-text text-muted">
                0 = tidak sama sekali, 1 = cukup membantu, 2 = sangat membantu
            </small>
        </div>
        <div class="mb-3">
            <label class="form-label">Saran dan Masukan Penyelenggara Training Sejenis</label>
            <textarea name="saran" class="form-control" rows="3">{{ old('saran', $evaluasi->saran) }}</textarea>
        </div>

        <button type="submit" class="btn btn-success">Update</button>
        <a href="{{ route('evaluasi-level-3.peserta.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>

{{-- Include the same dynamic row add/remove script as in create --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    let actionPlanRowCounter = {{ $evaluasi->actionPlans->count() }};

    const addButton = document.getElementById('add-action-plan');
    const tableBody = document.querySelector('#action-plan-table tbody');

    addButton.addEventListener('click', function(e) {
        e.preventDefault();

        const newRowHTML = `
            <tr data-row-index="${actionPlanRowCounter}">
                <td><input type="text" name="action_plan[${actionPlanRowCounter}]" class="form-control" required></td>
                <td class="text-center">
                    <input type="hidden" name="diaplikasikan[${actionPlanRowCounter}]" value="0">
                    <input type="checkbox" name="diaplikasikan[${actionPlanRowCounter}]" value="1">
                </td>
                <td><input type="number" name="frekuensi[${actionPlanRowCounter}]" class="form-control" min="0" max="3" value="0" required></td>
                <td><input type="number" name="hasil[${actionPlanRowCounter}]" class="form-control" min="1" max="4" value="1" required></td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm remove-row">Hapus</button>
                </td>
            </tr>
        `;

        tableBody.insertAdjacentHTML('beforeend', newRowHTML);
        actionPlanRowCounter++;
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-row')) {
            if (tableBody.rows.length > 1) {
                e.target.closest('tr').remove();
            } else {
                alert('Minimal harus ada satu baris Action Plan');
            }
        }
    });
});
</script>
@endsection
