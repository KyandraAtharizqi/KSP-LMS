@extends('layout.main')

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

        $('#editModal input#is_active').prop('checked', $(this).data('active') == 1);

        @if(auth()->user()->role === \App\Enums\Role::ADMIN->value)
            $('#editModal select[name="role"]').val($(this).data('role')).trigger('change');
        @endif
    });
</script>
@endpush

@section('content')
<x-breadcrumb :values="[__('menu.users')]">
    <button type="button" class="btn btn-primary btn-create" data-bs-toggle="modal" data-bs-target="#createModal">
        {{ __('menu.general.create') }}
    </button>
</x-breadcrumb>

<form method="GET" action="{{ route('user.index') }}" class="row g-3 mb-3">
    <div class="col-md-4">
        <input type="text" name="search" class="form-control" placeholder="Cari nama/email..." value="{{ request('search') }}">
    </div>
    <div class="col-md-3">
        <select name="jabatan_id" class="form-select">
            <option value="">-- Semua Jabatan --</option>
            @foreach ($jabatans as $jabatan)
                <option value="{{ $jabatan->id }}" {{ request('jabatan_id') == $jabatan->id ? 'selected' : '' }}>
                    {{ $jabatan->name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
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
        <button type="submit" class="btn btn-primary w-100">Filter</button>
    </div>
</form>

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

                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone }}</td>
                        <td>
                            <span class="badge bg-label-primary me-1">
                                {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td>
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
                                data-superior_registration_id="{{ optional($user->superior)->registration_id }}"
                                @if(auth()->user()->role === \App\Enums\Role::ADMIN->value)
                                    data-role="{{ $user->role }}"
                                @endif
                                data-bs-toggle="modal"
                                data-bs-target="#editModal">
                                Edit
                            </button>
                            <form action="{{ route('user.destroy', $user) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm btn-delete" type="submit">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="text-center">Data kosong</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Create Modal -->
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

                @if(auth()->user()->role === \App\Enums\Role::ADMIN->value)
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

<!-- Edit Modal -->
<div class="modal fade" id="editModal" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" method="post" action="">
            @csrf @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title">Edit Pengguna</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="id">
                <x-input-form name="name" label="Nama" />
                <x-input-form name="registration_id" label="Registration ID" />
                <x-input-form name="email" label="Email" type="email" />
                <x-input-form name="phone" label="Phone" />
                <x-input-form name="nik" label="NIK" />
                <x-input-form name="address" label="Alamat" type="textarea" />
                <x-input-form name="superior_registration_id" label="Superior Registration ID" />
                <x-select-form name="jabatan_id" label="Jabatan" :options="$jabatans" id="jabatan_id" />
                <x-input-form name="jabatan_full" label="Jabatan Lengkap (jika ada)" id="jabatan_full" />
                <x-select-form name="directorate_id" label="Direktorat (jika tanpa departemen)" :options="$directorates" id="directorate_id" />
                <x-select-form name="division_id" label="Divisi (jika ada)" :options="$divisions" id="division_id" />
                <x-select-form name="department_id" label="Departemen" :options="$departments" id="department_id" />

                @if(auth()->user()->role === \App\Enums\Role::ADMIN->value)
                    <select name="role" class="form-select mt-2">
                        @foreach ($roles as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
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
