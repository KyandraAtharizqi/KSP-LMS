@php
    $cardLink = '';
    // Training routes
    if($label == __('dashboard.surat_pengajuan') || $label == __('training.suratpengajuan.index')) {
        $cardLink = route('training.suratpengajuan.index');
    } elseif($label == __('dashboard.surat_tugas') || $label == __('training.surattugas.index')) {
        $cardLink = route('training.surattugas.index');
    } elseif($label == 'Pengajuan Disetujui' && request('type_filter') == 'training') {
        $cardLink = route('training.suratpengajuan.index') . '?status=approved';
    } elseif($label == 'Pengajuan Ditolak' && request('type_filter') == 'training') {
        $cardLink = route('training.suratpengajuan.index') . '?status=rejected';
    } elseif($label == 'Menunggu Persetujuan' && request('type_filter') == 'training') {
        $cardLink = route('training.suratpengajuan.index') . '?status=pending';
    }
    // Knowledge routes
    elseif($label == __('dashboard.pengajuan_knowledge') || $label == __('knowledge.pengajuan.index')) {
        $cardLink = route('knowledge.pengajuan.index');
    } elseif($label == __('dashboard.undangan') || $label == __('knowledge.undangan.index')) {
        $cardLink = route('knowledge.undangan.index');
    } elseif($label == 'Pengajuan Disetujui' && request('type_filter') == 'knowledge') {
        $cardLink = route('knowledge.pengajuan.index') . '?status=approved';
    } elseif($label == 'Pengajuan Ditolak' && request('type_filter') == 'knowledge') {
        $cardLink = route('knowledge.pengajuan.index') . '?status=rejected';
    } elseif($label == 'Menunggu Persetujuan' && request('type_filter') == 'knowledge') {
        $cardLink = route('knowledge.pengajuan.index') . '?status=pending';
    }
    // User routes
    elseif($label == __('dashboard.active_user')) {
        $cardLink = route('user.index');
    }
@endphp

@if($cardLink)
    <a href="{{ $cardLink }}" class="card-link">
@endif
<div class="card h-100 dashboard-card-hover">
    <div class="card-body text-center d-flex flex-column justify-content-center" style="min-height: 180px;">
        <!-- Icon di atas -->
        <div class="mb-3">
            <span class="badge bg-label-{{ $color }} p-3">
                <i class="bx {{ $icon }} text-{{ $color }}" style="font-size: 1.5rem;"></i>
            </span>
        </div>
        
        <!-- Label -->
        <span class="fw-semibold d-block mb-2">{{ $label }}</span>
        
        <!-- Value (angka utama) -->
        <h2 class="card-title mb-2 text-{{ $color }}">{{ $value }}</h2>
        
        <!-- Info tambahan -->
        @if(isset($acceptedCount) && $acceptedCount !== null)
            <small class="text-info fw-semibold d-block mb-1">
                <i class="bx bx-check-circle"></i> {{ $acceptedCount }} Disetujui
            </small>
        @endif
        
        @if($percentage > 0)
            <small class="text-success fw-semibold">
                <i class="bx bx-up-arrow-alt"></i> {{ $percentage }}%
            </small>
        @elseif($percentage < 0)
            <small class="text-danger fw-semibold">
                <i class="bx bx-down-arrow-alt"></i> {{ $percentage }}%
            </small>
        @endif
    </div>
</div>
@if($cardLink)
    </a>
@endif
