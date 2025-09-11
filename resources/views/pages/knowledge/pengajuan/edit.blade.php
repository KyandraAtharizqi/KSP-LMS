@extends('layout.main')

@section('title', 'Edit Pengajuan Knowledge Sharing')

@section('content')
<div class="page-heading">
    <h3>Edit Pengajuan</h3>
</div>

<div class="page-content">
    <div class="card">
        <div class="card-body">
            <form action="{{ route('knowledge.pengajuan.update', $pengajuan->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                {{-- Perihal --}}
                <div class="mb-3">
                    <label for="perihal" class="form-label">Perihal</label>
                    <input type="text" class="form-control" id="perihal" name="perihal" value="{{ old('perihal', $pengajuan->perihal) }}" required>
                </div>

                {{-- Pemateri --}}
                <div class="mb-3">
                    <label for="pemateri" class="form-label">Pemateri</label>
                    <input type="text" class="form-control" id="pemateri" name="pemateri" value="{{ old('pemateri', $pengajuan->pemateri) }}" required>
                </div>

                {{-- Tanggal dan Waktu --}}
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                        <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai"
                            value="{{ old('tanggal_mulai', $pengajuan->tanggal_mulai?->toDateString()) }}">
                    </div>
                    <div class="col-md-3">
                        <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
                        <input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai"
                            value="{{ old('tanggal_selesai', $pengajuan->tanggal_selesai?->toDateString()) }}">
                    </div>
                    <div class="col-md-3">
                        <label for="waktu_mulai" class="form-label">Jam Mulai</label>
                        <input type="time" class="form-control" id="waktu_mulai" name="waktu_mulai"
                            value="{{ old('waktu_mulai', $pengajuan->jam_mulai) }}">
                    </div>
                    <div class="col-md-3">
                        <label for="waktu_selesai" class="form-label">Jam Selesai</label>
                        <input type="time" class="form-control" id="waktu_selesai" name="waktu_selesai"
                            value="{{ old('waktu_selesai', $pengajuan->jam_selesai) }}">
                    </div>
                </div>

                {{-- Lampiran --}}
                <div class="mb-3">
                    <label for="lampiran" class="form-label">Lampiran (PDF)</label>
                    @if($pengajuan->lampiran)
                        <p><a href="{{ asset('storage/' . $pengajuan->lampiran) }}" target="_blank">Lihat Lampiran</a></p>
                    @endif
                    <input type="file" class="form-control" id="lampiran" name="lampiran" accept="application/pdf">
                </div>

                {{-- Peserta Baru --}}
                <div class="mb-3">
                    <label class="form-label">Peserta</label>
                    <div class="col-12 mb-2">
                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#participantModal">
                            + Tambah Peserta
                        </button>
                    </div>
                    <div class="col-12 mb-2" id="selected-participant-list"></div>
                    <div id="participant-inputs"></div>
                </div>

                <div class="text-end">
                    <a href="{{ route('knowledge.pengajuan.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Pilih Peserta --}}
<div class="modal fade" id="participantModal" tabindex="-1" aria-labelledby="participantModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Pilih Peserta</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="text" id="searchParticipant" class="form-control mb-3" placeholder="Cari nama / jabatan / departemen">

        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Nama</th>
              <th>Jabatan</th>
              <th>Departemen</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody id="participantTable">
            @foreach ($users as $u)
              <tr>
                <td>{{ $u->name }}</td>
                <td>{{ $u->jabatan_full ?? '-' }}</td>
                <td>{{ $u->department->name ?? '-' }}</td>
                <td>
                  <button type="button" class="btn btn-success btn-sm pilihPeserta"
                    data-id="{{ $u->id }}"
                    data-registration="{{ $u->registration_id }}"
                    data-name="{{ $u->name }}"
                    data-jabatan="{{ $u->jabatan_full ?? '-' }}"
                    data-department="{{ $u->department->name ?? '-' }}">
                    Pilih
                  </button>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection

<script>
let pesertaTerpilih = @json($pengajuan->peserta ?? []); // Untuk edit form, peserta lama dimuat

document.addEventListener('DOMContentLoaded', function () {
    renderPeserta();

    // Search di modal
    document.getElementById('searchParticipant').addEventListener('keyup', function () {
        let value = this.value.toLowerCase();
        document.querySelectorAll('#participantTable tr').forEach(function (row) {
            row.style.display = row.innerText.toLowerCase().includes(value) ? '' : 'none';
        });
    });

    // Pilih peserta baru
    document.querySelectorAll('.pilihPeserta').forEach(function (btn) {
        btn.addEventListener('click', function () {
            let id = this.dataset.id;
            if (!pesertaTerpilih.find(p => p.id == id)) {
                pesertaTerpilih.push({
                    id: id,
                    registration_id: this.dataset.registration,
                    name: this.dataset.name,
                    jabatan: this.dataset.jabatan,
                    department: this.dataset.department
                });
                renderPeserta();
            }
            // Tutup modal setelah pilih
            let modal = bootstrap.Modal.getInstance(document.getElementById('participantModal'));
            modal.hide();
        });
    });
});

// Hapus peserta
function hapusPeserta(id) {
    pesertaTerpilih = pesertaTerpilih.filter(p => p.id != id);
    renderPeserta();
}

// Render list peserta terpilih
function renderPeserta() {
    let listDiv = document.getElementById('selected-participant-list');
    let inputDiv = document.getElementById('participant-inputs');
    listDiv.innerHTML = '';
    inputDiv.innerHTML = '';

    if (pesertaTerpilih.length === 0) {
        listDiv.innerHTML = `<div class="text-muted">Belum ada peserta terdaftar.</div>`;
    } else {
        pesertaTerpilih.forEach(function (p) {
            listDiv.innerHTML += `
                <div class="d-flex justify-content-between align-items-center border p-2 mb-1">
                    <div>
                        <strong>${p.name}</strong><br>
                        <small>${p.jabatan} - ${p.department}</small>
                    </div>
                    <button type="button" class="btn btn-danger btn-sm" onclick="hapusPeserta('${p.id}')">Hapus</button>
                </div>
            `;
            inputDiv.innerHTML += `<input type="hidden" name="participants[]" value="${p.registration_id}">`;
        });
    }
}
</script>

