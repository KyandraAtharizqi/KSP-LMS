@extends('layout.main')

@push('style')
    <link rel="stylesheet" href="{{asset('sneat/vendor/libs/apex-charts/apex-charts.css')}}" />
    <style>
        .dashboard-card-hover {
            transition: all 0.3s ease;
            border: 1px solid #e7e7e7;
        }
        
        .dashboard-card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            border-color: #d4d4d4;
        }
        
        .dashboard-card-hover .card-body {
            transition: all 0.3s ease;
        }
        
        .dashboard-card-hover:hover .card-body {
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        }
        
        /* Card link styling */
        .card-link {
            text-decoration: none !important;
            color: inherit !important;
        }
        
        .card-link:hover {
            text-decoration: none !important;
            color: inherit !important;
        }
        
        .card-link:focus {
            text-decoration: none !important;
            color: inherit !important;
        }
        
        /* Responsive font sizes */
        @media (max-width: 768px) {
            .dashboard-card-hover .card-title {
                font-size: 1.5rem;
            }
            .dashboard-card-hover .card-body {
                min-height: 150px !important;
            }
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-lg-12 mb-4 order-0">
            <div class="card">
                <div class="d-flex align-items-end row">
                    <div class="col-sm-7">
                        <div class="card-body">
                            <h4 class="card-title text-primary">{{ $greeting }}</h4>
                            <p class="mb-4">
                                {{ $currentDate }}
                            </p>
                        </div>
                    </div>
                    <div class="col-sm-5 text-center text-sm-left">
                        <div class="card-body pb-0 px-0 px-md-4">
                            <img src="{{asset('sneat/img/man-with-laptop-light.png')}}" height="140"
                                 alt="View Badge User" data-app-dark-img="illustrations/man-with-laptop-dark.png"
                                 data-app-light-img="illustrations/man-with-laptop-light.png">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12 mb-4">
            <div class="card">
                <form action="{{ route('dashboard') }}" method="GET" id="typeFilterForm">
                    <div class="roalign-items-center">
                        <select class="form-select" name="type_filter" id="type_filter" onchange="this.form.submit()">
                            <option value="" {{ request('type_filter') == '' ? 'selected' : '' }}>Semua Kegiatan</option>
                            <option value="training" {{ request('type_filter') == 'training' ? 'selected' : '' }}>Training</option>
                            <option value="knowledge" {{ request('type_filter') == 'knowledge' ? 'selected' : '' }}>Knowledge Sharing</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="row">
                @if(request('type_filter') == '' || request('type_filter') == 'training')
                    <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                        <x-dashboard-card-simple
                            :label="__('dashboard.surat_pengajuan')"
                            :value="$totalSuratPengajuan"
                            :daily="false"
                            color="primary"
                            icon="bx-envelope"
                            :percentage="$percentageSuratPengajuan"
                            :acceptedCount="$acceptedSuratPengajuan"
                        />
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                        <x-dashboard-card-simple
                            :label="__('dashboard.surat_tugas')"
                            :value="$totalSuratTugas"
                            :daily="false"
                            color="warning"
                            icon="bx-mail-send"
                            :percentage="$percentageSuratTugas"
                            :acceptedCount="$acceptedSuratTugas"
                        />
                    </div>
                @endif

                @if(request('type_filter') == 'training')
                    <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                        <x-dashboard-card-simple
                            label="Pengajuan Disetujui"
                            :value="$acceptedSuratPengajuan"
                            :daily="false"
                            color="success"
                            icon="bx-check-circle"
                            :percentage="0"
                        />
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                        <x-dashboard-card-simple
                            label="Pengajuan Ditolak"
                            :value="$rejectedSuratPengajuan ?? 0"
                            :daily="false"
                            color="danger"
                            icon="bx-x-circle"
                            :percentage="0"
                        />
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                        <x-dashboard-card-simple
                            label="Menunggu Persetujuan"
                            :value="$pendingSuratPengajuan ?? 0"
                            :daily="false"
                            color="warning"
                            icon="bx-time-five"
                            :percentage="0"
                        />
                    </div>
                @endif

                @if(request('type_filter') == '' || request('type_filter') == 'knowledge')
                    <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                        <x-dashboard-card-simple
                            :label="__('dashboard.pengajuan_knowledge')"
                            :value="$totalKnowledgeLetter"
                            :daily="false"
                            color="primary"
                            icon="bx-envelope"
                            :percentage="$percentageKnowledgeLetter"
                            :acceptedCount="$acceptedKnowledgeLetter"
                        />
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                        <x-dashboard-card-simple
                            :label="__('dashboard.undangan')"
                            :value="$totalSuratUndangan"
                            :daily="false"
                            color="warning"
                            icon="bx-mail-send"
                            :percentage="$percentageSuratUndangan"
                            :acceptedCount="$acceptedSuratUndangan"
                        />
                    </div>
                @endif

                @if(request('type_filter') == 'knowledge')
                    <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                        <x-dashboard-card-simple
                            label="Pengajuan Disetujui"
                            :value="$acceptedPengajuanKnowledge"
                            :daily="false"
                            color="success"
                            icon="bx-check-circle"
                            :percentage="0"
                        />
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                        <x-dashboard-card-simple
                            label="Pengajuan Ditolak"
                            :value="$rejectedPengajuanKnowledge ?? 0"
                            :daily="false"
                            color="danger"
                            icon="bx-x-circle"
                            :percentage="0"
                        />
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                        <x-dashboard-card-simple
                            label="Menunggu Persetujuan"
                            :value="$pendingPengajuanKnowledge ?? 0"
                            :daily="false"
                            color="warning"
                            icon="bx-time-five"
                            :percentage="0"
                        />
                    </div>
                @endif

                @if(request('type_filter') == '' && auth()->user()->role == 'admin')
                    <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                        <x-dashboard-card-simple
                            :label="__('dashboard.active_user')"
                            :value="$activeUser"
                            :daily="false"
                            color="info"
                            icon="bx-user-check"
                            :percentage="0"
                        />
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
