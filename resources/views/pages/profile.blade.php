@extends('layout.main')

@push('script')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
    $('input#accountActivation').on('change', function () {
        $('button.deactivate-account').attr('disabled', !$(this).is(':checked'));
    });

    document.addEventListener('DOMContentLoaded', function () {
        const signaturePad = new SignaturePad(document.getElementById('signature-pad'));
        const parafPad = new SignaturePad(document.getElementById('paraf-pad'));

        document.getElementById('clear-signature').onclick = () => signaturePad.clear();
        document.getElementById('clear-paraf').onclick = () => parafPad.clear();

        window.saveSignature = function () {
            if (!signaturePad.isEmpty()) {
                document.getElementById('signature_data').value = signaturePad.toDataURL('image/png');
            }
        };

        window.saveParaf = function () {
            if (!parafPad.isEmpty()) {
                document.getElementById('paraf_data').value = parafPad.toDataURL('image/png');
            }
        };
    });
</script>
@endpush

@section('content')
<x-breadcrumb :values="[__('navbar.profile.profile')]"></x-breadcrumb>

@if ($errors->any())
    <div class="alert alert-danger">
        <h5 class="alert-heading">Terdapat Kesalahan Validasi:</h5>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row">
    <div class="col">
        @if(auth()->user()->role == 'admin')
        <ul class="nav nav-pills flex-column flex-md-row mb-3">
            <li class="nav-item">
                <a class="nav-link active" href="javascript:void(0);">{{ __('navbar.profile.profile') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('settings.show') }}">{{ __('navbar.profile.settings') }}</a>
            </li>
        </ul>
        @endif

        <div class="card mb-4">
            <form action="{{ route('profile.update') }}" method="post" enctype="multipart/form-data">
                @csrf

                <div class="card-body">
                    <div class="d-flex align-items-start align-items-sm-center gap-4">
                        @php
                            $picture = $data->profile_picture;
                            // Cek apakah $picture ada isinya dan BUKAN sebuah URL lengkap
                            if ($picture && !Illuminate\Support\Str::startsWith($picture, 'http')) {
                                // Jika ini path lokal, maka kita tambahkan asset storage
                                $pictureUrl = asset('storage/' . $picture);
                            } else {
                                // Jika ini SUDAH URL lengkap (cth: dari ui-avatars) ATAU kosong,
                                // kita gunakan langsung atau berikan gambar default.
                                $pictureUrl = $picture ?: asset('assets/img/avatars/1.png');
                            }
                        @endphp

                        <img src="{{ $pictureUrl }}" alt="user-avatar" class="d-block rounded" height="100" width="100" id="uploadedAvatar">
                        <div class="button-wrapper">
                            <label for="upload" class="btn btn-primary me-2 mb-4" tabindex="0">
                                <span class="d-none d-sm-block">{{ __('menu.general.upload') }}</span>
                                <i class="bx bx-upload d-block d-sm-none"></i>
                                <input type="file" name="profile_picture" id="upload" class="account-file-input" hidden=""
                                       accept="image/png, image/jpeg">
                            </label>
                            <button type="button" class="btn btn-outline-secondary account-image-reset mb-4">
                                <i class="bx bx-reset d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">{{ __('menu.general.cancel') }}</span>
                            </button>
                            <p class="text-muted mb-0">< 800K (JPG, GIF, PNG)</p>
                        </div>
                    </div>
                </div>
                <hr class="my-0">
                <div class="card-body">
                    <div class="row">
                        <input type="hidden" name="id" value="{{ $data->id }}">
                        <div class="col-md-6 col-lg-12">
                            <x-input-form name="name" :label="__('model.user.name')" :value="$data->name" />
                        </div>
                        <div class="col-md-6">
                            <x-input-form name="email" :label="__('model.user.email')" :value="$data->email" />
                        </div>
                        <div class="col-md-6">
                            <x-input-form name="phone" :label="__('model.user.phone')" :value="$data->phone ?? ''" />
                        </div>

                        {{-- Signature --}}
                        <div class="col-md-6">
                            <label class="form-label d-block">Tanda Tangan</label>
                            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#signatureModal">
                                Tambah Tanda Tangan
                            </button>
                            <div class="mt-2">
                                @if ($data->signatureParaf && $data->signatureParaf->signature_path)
                                    <img src="{{ asset('storage/' . $data->signatureParaf->signature_path) }}" alt="Signature" height="100">
                                @else
                                    <p class="text-muted">Belum diunggah.</p>
                                @endif
                            </div>
                        </div>

                        {{-- Paraf --}}
                        <div class="col-md-6">
                            <label class="form-label d-block">Paraf</label>
                            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#parafModal">
                                Tambah Paraf
                            </button>
                            <div class="mt-2">
                                @if ($data->signatureParaf && $data->signatureParaf->paraf_path)
                                    <img src="{{ asset('storage/' . $data->signatureParaf->paraf_path) }}" alt="Paraf" height="100">
                                @else
                                    <p class="text-muted">Belum diunggah.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="mt-2">
                        <button type="submit" class="btn btn-primary me-2">{{ __('menu.general.update') }}</button>
                        <button type="reset" class="btn btn-outline-secondary">{{ __('menu.general.cancel') }}</button>
                    </div>
                </div>
            </form>
        </div>

        @if(auth()->user()->role == 'staff')
        <div class="card">
            <h5 class="card-header">{{ __('navbar.profile.deactivate_account') }}</h5>
            <div class="card-body">
                <div class="mb-3 col-12 mb-0">
                    <div class="alert alert-warning">
                        <h6 class="alert-heading fw-bold mb-1">{{ __('navbar.profile.deactivate_confirm_message') }}</h6>
                    </div>
                </div>
                <form id="formAccountDeactivation" action="{{ route('profile.deactivate') }}" method="post">
                    @csrf
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="accountActivation" id="accountActivation">
                        <label class="form-check-label" for="accountActivation">{{ __('navbar.profile.deactivate_confirm') }}</label>
                    </div>
                    <button type="submit" class="btn btn-danger deactivate-account" disabled>{{ __('navbar.profile.deactivate_account') }}</button>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Signature Modal --}}
<div class="modal fade" id="signatureModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('profile.upload.signature') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tanda Tangan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <canvas id="signature-pad" style="border:1px solid #ddd;" width="400" height="200"></canvas>
                    <input type="hidden" name="signature_data" id="signature_data">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="clear-signature">Clear</button>
                    <button type="submit" class="btn btn-primary" onclick="saveSignature()">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Paraf Modal --}}
<div class="modal fade" id="parafModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('profile.upload.paraf') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Paraf</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <canvas id="paraf-pad" style="border:1px solid #ddd;" width="400" height="200"></canvas>
                    <input type="hidden" name="paraf_data" id="paraf_data">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="clear-paraf">Clear</button>
                    <button type="submit" class="btn btn-primary" onclick="saveParaf()">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
