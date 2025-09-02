@php
    $cardLink = '';
    if($label == __('dashboard.incoming_letter')) {
        $cardLink = route('training.suratpengajuan.index');
    } elseif($label == __('dashboard.outgoing_letter')) {
        $cardLink = route('training.surattugas.index');
    } elseif($label == __('dashboard.disposition_letter')) {
        $cardLink = route('knowledge.pengajuan.index');
    } elseif($label == __('dashboard.active_user')) {
        $cardLink = route('user.index');
    }
@endphp

@if($cardLink)
    <a href="{{ $cardLink }}" class="text-decoration-none">
@endif
<div class="card">
    <div class="card-body">
        <div class="card-title d-flex align-items-start justify-content-between">
            <div class="avatar flex-shrink-0">
                <span class="badge bg-label-{{ $color }} p-2">
                    <i class="bx {{ $icon }} text-{{ $color }}"></i>
                </span>
            </div>
            @if($label != __('dashboard.disposition_letter') && $label != __('dashboard.incoming_letter') && $label != __('dashboard.outgoing_letter') && !(auth()->user()->role == 'staff' && $label == __('dashboard.active_user')))
                <div class="dropdown">
                    <button class="btn p-0" type="button" id="cardOpt3" data-bs-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                        <i class="bx bx-dots-vertical-rounded"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt3">
                        @if($label == __('dashboard.active_user'))
                            <a class="dropdown-item"
                               href="{{ route('user.index') }}">{{ __('dashboard.view_more') }}</a>
                        @endif
                    </div>
                </div>
            @endif
        </div>
        <span class="fw-semibold d-block mb-1">{{ $label }}</span>
        <h3 class="card-title mb-2">{{ $value }}</h3>
        @if(isset($acceptedCount) && $acceptedCount !== null)
            <small class="text-info fw-semibold d-block mb-1">
                <i class="bx bx-check-circle"></i> {{ $acceptedCount }} Disetujui
            </small>
        @endif
        @if($percentage > 0)
            <small class="text-success fw-semibold"><i class="bx bx-up-arrow-alt"></i> {{ $percentage }}%</small>
        @elseif($percentage < 0)
            <small class="text-danger fw-semibold"><i class="bx bx-down-arrow-alt"></i> {{ $percentage }}%</small>
        @endif
    </div>
</div>
@if($cardLink)
    </a>
@endif
