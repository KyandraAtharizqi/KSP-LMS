@extends('layout.main')

@section('title', 'User Management')

@push('script')
<script>
    $(document).on('click', '.btn-edit', function () {
        const id = $(this).data('id');
        $('#editModal form').attr('action', '{{ route('user.index') }}/' + id);
        $('#editModal input#id').val(id);

        $('#editModal input#name').val($(this).data('name'));
        $('#editModal input#registration_id').val($(this).data('registration_id'));
        $('#editModal input#email').val($(this).data('email'));
        $('#editModal input#phone').val($(this).data('phone'));
        $('#editModal input#nik').val($(this).data('nik'));
        $('#editModal input#address').val($(this).data('address'));
        $('#editModal input#superior_registration_id').val($(this).data('superior_registration_id'));

        $('#editModal select#jabatan_id').val($(this).data('jabatan_id')).trigger('change');
        $('#editModal input#jabatan_full').val($(this).data('jabatan_full'));
        $('#editModal select#directorate_id').val($(this).data('directorate_id')).trigger('change');
        $('#editModal select#division_id').val($(this).data('division_id')).trigger('change');
        $('#editModal select#department_id').val($(this).data('department_id')).trigger('change');
        $('#editModal input#golongan').val($(this).data('golongan'));

        $('#editModal input#is_active').prop('checked', $(this).data('active') == 1);

        @if(auth()->user()->role === \App\Enums\Role::ADMIN)
            $('#editModal select[name="role"]').val($(this).data('role')).trigger('change');
        @endif
    });

    // Fixed showHistory function - only this function is changed
    function showHistory(userId, histories) {
        console.log('=== SHOW HISTORY FUNCTION ===');
        console.log('User ID:', userId);
        console.log('Histories:', histories);
        console.log('Type:', typeof histories);
        console.log('Is array?', Array.isArray(histories));
        console.log('Length:', histories ? histories.length : 'N/A');
        
        const modalBody = $('#historyModal tbody');
        modalBody.empty();

        if (!histories || !Array.isArray(histories) || histories.length === 0) {
            console.log('No history data found');
            modalBody.append('<tr><td colspan="9" class="text-center py-3">No history available.</td></tr>');
        } else {
            console.log('Processing', histories.length, 'history items');
            
            // Sort by effective_date descending (newest first)
            histories.sort((a, b) => {
                const dateA = new Date(a.effective_date || a.created_at);
                const dateB = new Date(b.effective_date || b.created_at);
                return dateB - dateA;
            });
            
            histories.forEach((item, index) => {
                console.log(`Item ${index + 1}:`, item);
                
                const jabatanName = item.jabatan_name || '-';
                const jabatanFull = item.jabatan_full || '-';
                const departmentName = item.department_name || '-';
                const divisionName = item.division_name || '-';  
                const directorateName = item.directorate_name || '-';
                const golongan = item.golongan || '-';
                const effectiveDate = item.effective_date || item.created_at || '-';
                const status = item.is_active ? 'Active' : 'Inactive';
                
                modalBody.append(`
                    <tr ${item.is_active ? 'class="table-success"' : ''}>
                        <td class="text-center">${index + 1}</td>
                        <td><strong>${jabatanName}</strong></td>
                        <td><small class="text-muted">${jabatanFull}</small></td>
                        <td>${departmentName}</td>
                        <td>${divisionName}</td>
                        <td>${directorateName}</td>
                        <td class="text-center">${golongan !== '-' ? `<span class="badge bg-info">${golongan}</span>` : '-'}</td>
                        <td class="text-center">${effectiveDate}</td>
                        <td class="text-center"><span class="badge ${item.is_active ? 'bg-success' : 'bg-secondary'}">${status}</span></td>
                    </tr>
                `);
            });
        }

        $('#historyModal').modal('show');
    }

    // Backup click handler (not needed since we use onclick, but keeping for safety)
    $(document).on('click', '.btn-history', function () {
        console.log('Backup click handler triggered');
        // This will only run if onclick fails
        const userId = $(this).data('user-id');
        console.log('Trying to show history for user:', userId);
    });
</script>
@endpush

@section('content')
<x-breadcrumb :values="[__('menu.users')]">
    <!-- CSV Import Button -->
    <button type="button" class="btn btn-success ms-2" data-bs-toggle="modal" data-bs-target="#importCsvModal">
        Import CSV
    </button>

    <!-- CSV Import Modal -->
    <div class="modal fade" id="importCsvModal" tabindex="-1" aria-labelledby="importCsvModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('users.import.csv') }}" enctype="multipart/form-data" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="importCsvModalLabel">Import Pengguna dari CSV</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="csv_file" class="form-label">File CSV</label>
                        <input type="file" name="csv_file" id="csv_file" class="form-control" accept=".csv" required>
                        <small class="form-text text-muted">
                            Format kolom: Registration_ID, Nama, Alamat, Jabatan_Full, Golongan, Direktorat, Jabatan
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Import</button>
                </div>
            </form>
        </div>
    </div>

    <button type="button" class="btn btn-primary btn-create" data-bs-toggle="modal" data-bs-target="#createModal">
        {{ __('menu.general.create') }}
    </button>
</x-breadcrumb>

@if(session('success'))
    <div class="alert alert-success mt-2">
        {{ session('success') }}
    </div>
@endif

<form method="GET" action="{{ route('user.index') }}" class="row g-3 mb-3">
    <div class="col-md-4">
        <input type="text" name="search" class="form-control" placeholder="Cari nama/email..." value="{{ request('search') }}">
    </div>

    <div class="col-md-2">
        <select name="jabatan_id" class="form-select">
            <option value="">-- Semua Jabatan --</option>
            @foreach ($jabatans as $jabatan)
                <option value="{{ $jabatan->id }}" {{ request('jabatan_id') == $jabatan->id ? 'selected' : '' }}>
                    {{ $jabatan->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-2">
        <select name="directorate_id" class="form-select">
            <option value="">-- Semua Direktorat --</option>
            @foreach ($directorates as $dir)
                <option value="{{ $dir->id }}" {{ request('directorate_id') == $dir->id ? 'selected' : '' }}>
                    {{ $dir->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-2">
        <select name="division_id" class="form-select">
            <option value="">-- Semua Divisi --</option>
            @foreach ($divisions as $div)
                <option value="{{ $div->id }}" {{ request('division_id') == $div->id ? 'selected' : '' }}>
                    {{ $div->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-2">
        <select name="department_id" class="form-select">
            <option value="">-- Semua Departemen --</option>
            @foreach ($departments as $dept)
                <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                    {{ $dept->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-2">
        <select name="golongan" class="form-select">
            <option value="">-- Semua Golongan --</option>
            @foreach ($golongans as $gol)
                <option value="{{ $gol }}" {{ request('golongan') == $gol ? 'selected' : '' }}>
                    Golongan {{ $gol }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-2">
        <button type="submit" class="btn btn-primary w-100">Filter</button>
    </div>
</form>

<!-- Table Display -->
<div class="card mb-5">
    <div class="table-responsive text-nowrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Registration ID</th>
                    <th>Jabatan</th>
                    <th>Jabatan Lengkap</th>
                    <th>Direktorat</th>
                    <th>Divisi</th>
                    <th>Departemen</th>
                    <th>Golongan</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->registration_id }}</td>
                        <td>{{ optional($user->jabatan)->name ?? '-' }}</td>
                        <td>{{ $user->jabatan_full ?? '-' }}</td>
                        <td>{{ optional($user->directorate)->name ?? optional($user->department?->directorate)->name ?? '-' }}</td>
                        <td>{{ optional($user->division)->name ?? optional($user->department?->division)->name ?? '-' }}</td>
                        <td>{{ optional($user->department)->name ?? '-' }}</td>
                        <td>{{ $user->golongan ?? '-' }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone }}</td>
                        <td>
                            <span class="badge bg-label-primary me-1">
                                {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-warning btn-sm btn-history"
                                data-user-id="{{ $user->id }}"
                                onclick="showHistory({{ $user->id }}, {{ json_encode($user->positionHistories->map(function($history) {
                                    return [
                                        'id' => $history->id,
                                        'jabatan_name' => $history->jabatan?->name ?? '-',
                                        'jabatan_full' => $history->jabatan_full ?? '-',
                                        'department_name' => $history->department?->name ?? '-', 
                                        'division_name' => $history->division?->name ?? '-',
                                        'directorate_name' => $history->directorate?->name ?? '-',
                                        'effective_date' => $history->effective_date?->format('Y-m-d') ?? '-',
                                        'recorded_at' => $history->recorded_at?->format('Y-m-d H:i:s') ?? '-',
                                        'created_at' => $history->created_at?->format('Y-m-d H:i:s') ?? '-',
                                        'is_active' => $history->is_active,
                                        'golongan' => $history->golongan ?? '-',
                                        'superior_name' => $history->superior?->name ?? '-'
                                    ];
                                })) }})"
                            >
                                History
                            </button>

                            <button class="btn btn-info btn-sm btn-edit"
                                data-id="{{ $user->id }}"
                                data-name="{{ $user->name }}"
                                data-email="{{ $user->email }}"
                                data-phone="{{ $user->phone }}"
                                data-active="{{ $user->is_active }}"
                                data-registration_id="{{ $user->registration_id }}"
                                data-nik="{{ $user->nik }}"
                                data-address="{{ $user->address }}"
                                data-jabatan_id="{{ $user->jabatan_id }}"
                                data-jabatan_full="{{ $user->jabatan_full }}"
                                data-directorate_id="{{ $user->directorate_id }}"
                                data-division_id="{{ $user->division_id }}"
                                data-department_id="{{ $user->department_id }}"
                                data-golongan="{{ $user->golongan }}"
                                data-superior_registration_id="{{ optional($user->superior)->registration_id }}"
                                @if(auth()->user()->role === 'ADMIN')
                                    data-role="{{ $user->role }}"
                                @endif
                                data-bs-toggle="modal"
                                data-bs-target="#editModal">
                                Edit
                            </button>

                            <form action="{{ route('user.destroy', $user) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm btn-delete" type="submit" 
                                    onclick="return confirm('Are you sure you want to delete this user?')">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" class="text-center">Data kosong</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Adjusted Create Modal -->
<div class="modal fade" id="createModal" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" method="post" action="{{ route('user.store') }}">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Tambah Pengguna</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <x-input-form name="name" label="Nama" />
                <x-input-form name="registration_id" label="Registration ID" />
                <x-input-form name="email" label="Email" type="email" />
                <x-input-form name="phone" label="Phone" />
                <x-input-form name="nik" label="NIK" />
                <x-input-form name="address" label="Alamat" type="textarea" />
                <x-input-form name="superior_registration_id" label="Superior Registration ID" />
                <x-select-form name="jabatan_id" label="Jabatan" :options="$jabatans" id="jabatan_id_create" />
                <x-input-form name="jabatan_full" label="Jabatan Lengkap (jika ada)" />
                <x-select-form name="directorate_id" label="Direktorat (jika tanpa departemen)" :options="$directorates" id="directorate_id_create" />
                <x-select-form name="division_id" label="Divisi (jika ada)" :options="$divisions" id="division_id_create" />
                <x-select-form name="department_id" label="Departemen" :options="$departments" id="department_id_create" />
                <x-input-form name="golongan" label="Golongan (opsional)" id="golongan" />
                <x-input-form name="effective_date" label="Tanggal Efektif Posisi" type="date" />

                @if(auth()->user()->role === \App\Enums\Role::ADMIN)
                    <select name="role" class="form-select mt-2">
                        @foreach ($roles as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                @endif

                <div class="form-check mt-2">
                    <input class="form-check-input" type="checkbox" name="is_active" value="true" id="is_active_create">
                    <label class="form-check-label" for="is_active_create">Aktif</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>


{{-- Updated History Modal with white/transparent header --}}
<div class="modal fade" id="historyModal" tabindex="-1" aria-labelledby="historyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="historyModalLabel">Position History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">#</th>
                                <th>Jabatan</th>
                                <th>Full Position</th>
                                <th>Department</th>
                                <th>Division</th>
                                <th>Directorate</th>
                                <th class="text-center">Golongan</th>
                                <th class="text-center">Effective Date</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Adjusted Edit Modal -->
<div class="modal fade" id="editModal" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form class="modal-content" method="post" action="">
            @csrf @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title">Edit Pengguna</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" name="id" id="id">

                {{-- ================= PROFILE (not recorded in history) ================= --}}
                <h6 class="fw-bold text-primary mb-2">👤 Informasi Umum</h6>
                <x-input-form name="name" label="Nama" />
                <x-input-form name="registration_id" label="Registration ID" />
                <x-input-form name="email" label="Email" type="email" />
                <x-input-form name="phone" label="Phone" />
                <x-input-form name="nik" label="NIK" />
                <x-input-form name="address" label="Alamat" type="textarea" />

                <hr class="my-3">

                {{-- ================= POSITION (logged in UserPositionHistory) ================= --}}
                <h6 class="fw-bold text-success mb-2">🏢 Posisi & Struktur Organisasi</h6>
                <x-input-form name="superior_registration_id" label="Superior Registration ID" />
                <x-select-form name="jabatan_id" label="Jabatan" :options="$jabatans" id="jabatan_id" />
                <x-input-form name="jabatan_full" label="Jabatan Lengkap (jika ada)" id="jabatan_full" />
                <x-select-form name="directorate_id" label="Direktorat (jika tanpa departemen)" :options="$directorates" id="directorate_id" />
                <x-select-form name="division_id" label="Divisi (jika ada)" :options="$divisions" id="division_id" />
                <x-select-form name="department_id" label="Departemen" :options="$departments" id="department_id" />
                <x-input-form name="golongan" label="Golongan (opsional)" id="golongan" />
                <x-input-form name="effective_date" label="Tanggal Efektif Posisi" type="date" />

                <hr class="my-3">

                {{-- ================= SYSTEM & ROLE (not recorded in history) ================= --}}
                <h6 class="fw-bold text-warning mb-2">⚙️ Pengaturan Sistem</h6>
                @if(auth()->user()->role === \App\Enums\Role::ADMIN)
                    <div class="mb-2">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select">
                            @foreach ($roles as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="form-check mt-2">
                    <input class="form-check-input" type="checkbox" name="is_active" value="true" id="is_active">
                    <label class="form-check-label" for="is_active">Aktif</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="reset_password" value="true" id="reset_password">
                    <label class="form-check-label" for="reset_password">Reset Password</label>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Perbarui</button>
            </div>
        </form>
    </div>
</div>
@endsection
