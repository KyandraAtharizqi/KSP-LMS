@extends('layout.main')

@push('style')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
<style>
/* CSS khusus untuk Cropper.js agar gambar optimal */
#cropper-image {
    max-width: 100%;
    max-height: 100%;
    width: auto;
    height: auto;
    object-fit: contain;
    display: block;
    margin: 0 auto;
}

/* Container cropper responsif */
.cropper-container {
    max-width: 100%;
    max-height: 100%;
}

/* Pastikan crop box terlihat dengan baik */
.cropper-crop-box {
    border: 2px solid #007bff;
}

.cropper-view-box {
    outline: 1px solid #007bff;
}
</style>
@endpush

@push('script')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<script>
    $('input#accountActivation').on('change', function () {
        $('button.deactivate-account').attr('disabled', !$(this).is(':checked'));
    });
    
    // Reset image
    $('.account-image-reset').on('click', function() {
        $('#uploadedAvatar').attr('src', 'https://ui-avatars.com/api/?background=6D67E4&color=fff&name={{ urlencode($data->name) }}');
        // Buat input hidden untuk menandai bahwa avatar harus direset
        if ($('input[name="reset_avatar"]').length === 0) {
            $('<input>').attr({
                type: 'hidden',
                name: 'reset_avatar',
                value: '1'
            }).appendTo('form');
        }
        
        // Hapus nama file yang ditampilkan
        $('.button-wrapper .file-name').text('');
        
        // Reset input file
        $('#upload').val('');
    });
    
    // Preview image before upload with crop feature
    let cropper;
    
    $('#upload').on('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                // Set gambar ke element img
                $('#cropper-image').attr('src', e.target.result);
                // Tampilkan modal crop
                $('#cropModal').modal('show');
            };
            reader.readAsDataURL(file);
            
            // Hapus input reset_avatar jika ada
            $('input[name="reset_avatar"]').remove();
            
            // Tampilkan nama file yang dipilih
            $('.button-wrapper .file-name').text('File terpilih: ' + file.name + ' (Menunggu crop...)');
        }
    });
    
    // Handle crop modal events
    $('#cropModal').on('hidden.bs.modal', function () {
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
        // Reset file input jika user cancel
        if (!$('#uploadedAvatar').attr('src').includes('data:image')) {
            $('#upload').val('');
            $('.button-wrapper .file-name').text('');
        }
    });
    
    // Handle crop button
    $('#cropBtn').on('click', function() {
        if (cropper) {
            const canvas = cropper.getCroppedCanvas({
                width: 400,
                height: 400,
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high',
            });
            
            // Convert canvas to blob
            canvas.toBlob(function(blob) {
                // Preview di avatar
                const croppedImageUrl = canvas.toDataURL('image/png');
                $('#uploadedAvatar').attr('src', croppedImageUrl);
                
                // Create new file dari hasil crop
                const fileName = 'cropped-profile-picture.png';
                const croppedFile = new File([blob], fileName, {
                    type: 'image/png',
                    lastModified: Date.now()
                });
                
                // Replace file input dengan file hasil crop
                const fileInput = document.getElementById('upload');
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(croppedFile);
                fileInput.files = dataTransfer.files;
                
                // Update file name display
                $('.button-wrapper .file-name').text('File siap upload: ' + fileName);
                
                // Tutup modal
                $('#cropModal').modal('hide');
            }, 'image/png', 0.9);
        }
    });
    
    // Handle modal events untuk Cropper.js
    $('#cropModal').on('shown.bs.modal', function () {
        // Destroy cropper sebelumnya jika ada
        if (cropper) {
            cropper.destroy();
        }
        
        // Inisialisasi cropper baru dengan konfigurasi yang tepat
        const image = document.getElementById('cropper-image');
        cropper = new Cropper(image, {
            aspectRatio: 1, // Rasio 1:1 untuk kotak
            viewMode: 0, // Tidak ada batasan - crop box bisa bebas bergerak
            dragMode: 'crop', // Mode drag untuk crop box
            autoCropArea: 0.8, // Ukuran crop area 80% agar ada ruang untuk geser
            responsive: true,
            restore: false,
            guides: true,
            center: true,
            highlight: true,
            cropBoxMovable: true, // Crop box bisa digeser
            cropBoxResizable: true, // Crop box bisa di-resize
            toggleDragModeOnDblclick: false,
            background: true, // Tampilkan background untuk area di luar crop
            ready: function () {
                // Setelah cropper ready, zoom gambar agar fit ke container
                setTimeout(() => {
                    // Zoom otomatis agar gambar memenuhi container
                    const containerData = cropper.getContainerData();
                    const imageData = cropper.getImageData();
                    
                    // Hitung zoom ratio yang dibutuhkan (lebih konservatif)
                    const zoomRatio = Math.min(
                        containerData.width / imageData.naturalWidth,
                        containerData.height / imageData.naturalHeight
                    ) * 0.9; // Kurangi ke 90% agar ada ruang untuk drag
                    
                    if (zoomRatio > 0) {
                        cropper.zoomTo(zoomRatio);
                    }
                }, 300);
            }
        });
    });
    
    $('#cropModal').on('hidden.bs.modal', function () {
        // Destroy cropper saat modal ditutup
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
    });
    
    // Toggle password visibility
    document.addEventListener('DOMContentLoaded', function() {
        const toggles = document.querySelectorAll('.toggle-password');
        toggles.forEach(toggle => {
            toggle.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const passwordInput = document.getElementById(targetId);
                
                // Toggle type attribute
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                // Toggle icon
                const icon = this.querySelector('i');
                if (type === 'password') {
                    icon.className = 'bx bx-hide';
                } else {
                    icon.className = 'bx bx-show';
                }
            });
        });
        
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
                <!-- Gunakan metode POST untuk upload file -->
                <!-- @method('PUT') -->
                <div class="card-body">
                    <div class="d-flex align-items-start align-items-sm-center gap-4">
                        @php
                            $picture = $data->profile_picture;
                            // Cek apakah $picture ada isinya dan BUKAN sebuah URL lengkap
                            if ($picture && !str_starts_with($picture, 'http')) {
                                // Jika ini path lokal, maka kita tambahkan asset storage
                                $pictureUrl = asset('storage/' . $picture);
                            } else {
                                // Jika ini SUDAH URL lengkap (cth: dari ui-avatars) ATAU kosong,
                                // kita gunakan langsung atau berikan gambar default.
                                $pictureUrl = $picture ?: asset('assets/img/avatars/1.png');
                            }
                        @endphp
                        
                        <img src="{{ $pictureUrl }}" alt="user-avatar"
                            class="d-block rounded" height="100" width="100" id="uploadedAvatar">
                        <div class="button-wrapper">
                            <label for="upload" class="btn btn-primary me-2 mb-4" tabindex="0">
                                <span class="d-none d-sm-block">Unggah Foto</span>
                                <i class="bx bx-upload d-block d-sm-none"></i>
                                <input type="file" name="profile_picture" id="upload" class="account-file-input" hidden
                                    accept="image/png, image/jpeg">
                            </label>
                            <button type="button" class="btn btn-outline-secondary account-image-reset mb-4">
                                <i class="bx bx-reset d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">Hapus Foto</span>
                            </button>
                            <div>
                                <p class="text-muted mb-0">< 800K (JPG, GIF, PNG)</p>
                                <p class="text-primary file-name mt-2"></p>
                            </div>
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
                        <div class="col-md-6 col-lg-12">
                            <x-input-form name="email" :label="__('model.user.email')" :value="$data->email" />
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="current_password" class="form-label">Password Saat Ini</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="current_password" name="current_password">
                                    <span class="input-group-text toggle-password" data-target="current_password">
                                        <i class="bx bx-hide"></i>
                                    </span>
                                </div>
                                @error('current_password')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="new_password" class="form-label">Password Baru</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="new_password" name="new_password">
                                    <span class="input-group-text toggle-password" data-target="new_password">
                                        <i class="bx bx-hide"></i>
                                    </span>
                                </div>
                                @error('new_password')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="new_password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation">
                                    <span class="input-group-text toggle-password" data-target="new_password_confirmation">
                                        <i class="bx bx-hide"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <small class="text-muted">
                                Kosongkan field password jika tidak ingin mengubahnya. Jika diisi, password harus minimal 8 karakter, mengandung huruf besar, huruf kecil, dan angka.
                            </small>
                        </div>
                    </div>
                    
                    {{-- Signature --}}
                    <div class="row mt-4">
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
                    
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary me-2">Simpan</button>
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary">Kembali</a>
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
                    @method('PUT')
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

{{-- Crop Modal --}}
<div class="modal fade" id="cropModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 900px; width: 100%;">
        <div class="modal-content" style="height: 90vh;">
            <div class="modal-header py-3" style="padding-bottom: 1rem;">
                <h6 class="modal-title mb-0" style="margin-top: 0.25rem;">Crop Foto Profil</h6>
                <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal"
                        style="margin: 0; padding: 0.25rem;"></button>
            </div>
            <div class="modal-body text-center p-3 d-flex justify-content-center align-items-center">
                <div style="width: 100%; height: 55vh; overflow: hidden; border: 1px solid #ddd; border-radius: 8px;">
                    <img id="cropper-image"
                         style="width: 100%; height: 100%; object-fit: contain; display: block;">
                </div>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary btn-sm px-3" id="cropBtn">Potong & Gunakan</button>
            </div>
        </div>
    </div>
</div>

@endsection
