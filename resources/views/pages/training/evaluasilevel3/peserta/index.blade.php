@extends('layout.main')

@section('content')
<!-- Ensure CSRF token is available -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container">
    <h4 class="mb-4">Evaluasi Level 3 - Peserta</h4>

    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>Kode Pelatihan</th>
                <th>Internal ID</th>
                <th>Judul</th>
                <th>Penyelenggara</th>
                <th>Nama Peserta</th>
                <th>Atasan Sekarang</th>
                <th>Atasan (Saat Pengajuan)</th>
                <th class="text-center">Status</th>
                <th class="text-center">Aksi</th>
                @if(in_array(auth()->user()->role, ['admin', 'department_admin']))
                <th class="text-center">Admin</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse ($evaluasis as $evaluasi)
                @php
                    $pelatihan = $evaluasi['pelatihan'];
                    $user = $evaluasi['user'];
                @endphp
                <tr>
                    <td>{{ $pelatihan['kode_pelatihan'] }}</td>
                    <td>{{ $pelatihan['id'] }}</td>
                    <td>{{ $pelatihan['judul'] }}</td>
                    <td>{{ $pelatihan['penyelenggara'] }}</td>
                    <td>{{ $user['name'] ?? '-' }}</td>
                    <td>{{ $user['superior_id'] ?? '-' }}</td>
                    <td>{{ $evaluasi['pengajuan_superior_id'] ?? '-' }}</td>
                    <td class="text-center">
                        @if ($evaluasi['is_force_opened'])
                            <span class="badge bg-info fw-bold">
                                <i class="bx bx-key"></i> Dibuka Paksa
                            </span>
                        @elseif (!$evaluasi['is_allowed'] && $evaluasi['countdown_target'])
                            <span class="badge bg-secondary countdown-timer" 
                                  data-target="{{ $evaluasi['countdown_target'] }}"
                                  data-evaluasi-id="{{ $evaluasi['id'] }}">
                                <div class="countdown-display">
                                    <div style="text-align: center; font-size: 0.75rem; line-height: 1.2;">
                                        <div style="margin-bottom: 2px;">Dibuka dalam:</div>
                                        <div style="font-weight: bold;">
                                            <span class="days">{{ $evaluasi['days_remaining'] ?? 0 }}</span> hari
                                            <span class="hours">{{ str_pad($evaluasi['hours_remaining'] ?? 0, 2, '0', STR_PAD_LEFT) }}</span> jam<br>
                                            <span class="minutes">{{ str_pad($evaluasi['minutes_remaining'] ?? 0, 2, '0', STR_PAD_LEFT) }}</span> menit
                                            <span class="seconds">{{ str_pad($evaluasi['seconds_remaining'] ?? 0, 2, '0', STR_PAD_LEFT) }}</span> detik
                                        </div>
                                    </div>
                                </div>
                            </span>
                        @elseif (!$evaluasi['countdown_target'])
                            <span class="badge bg-secondary">Menunggu Data Kehadiran</span>
                        @else
                            @if (!$evaluasi['is_submitted'])
                                <span class="badge bg-warning text-dark fw-bold">Belum Selesai</span>
                            @else
                                <span class="badge bg-success fw-bold">Terkirim</span>
                            @endif
                        @endif
                    </td>
                    <td class="text-center">
                        @if ($evaluasi['is_allowed'])
                            @if (!$evaluasi['is_submitted'])
                               <a href="{{ route('evaluasi-level-3.peserta.edit', $pelatihan['id']) }}" class="btn btn-sm btn-primary">Lanjutkan</a>
                            @else
                                <a href="{{ route('evaluasi-level-3.peserta.preview', $evaluasi['id']) }}" class="btn btn-sm btn-outline-secondary mb-1"><i class="bx bx-show"></i> Preview</a>
                                <a href="{{ route('evaluasi-level-3.peserta.pdf', $evaluasi['id']) }}" class="btn btn-sm btn-success mb-1" target="_blank"><i class="bx bx-download"></i> PDF</a>
                            @endif
                        @else
                            <button class="btn btn-sm btn-secondary" disabled><i class="bx bx-lock"></i> Terkunci</button>
                        @endif
                    </td>
                    @if(in_array(auth()->user()->role, ['admin', 'department_admin']))
                    <td class="text-center">
                        @if (!$evaluasi['is_allowed'] && !$evaluasi['is_force_opened'])
                            <button class="btn btn-sm btn-warning force-open-btn" 
                                    data-evaluasi-id="{{ $evaluasi['id'] }}"
                                    data-pelatihan-name="{{ $pelatihan['judul'] }}"
                                    data-peserta-name="{{ $user['name'] }}">
                                <i class="bx bx-key"></i> Buka Paksa
                            </button>
                        @elseif ($evaluasi['is_force_opened'])
                            <span class="badge bg-success">
                                <i class="bx bx-check"></i> Sudah Dibuka
                            </span>
                        @else
                            <span class="badge bg-info">Normal</span>
                        @endif
                    </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ in_array(auth()->user()->role, ['admin', 'department_admin']) ? '10' : '6' }}" class="text-center py-4">
                        <div class="text-muted">
                            <i class="bx bx-inbox bx-lg"></i>
                            <p class="mt-2 mb-0">Tidak ada evaluasi tersedia.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>

<!-- Force Open Confirmation Modal -->
@if(in_array(auth()->user()->role, ['admin', 'department_admin']))
<div class="modal fade" id="forceOpenModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Buka Paksa Evaluasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin membuka paksa evaluasi ini?</p>
                <div class="alert alert-warning">
                    <strong>Pelatihan:</strong> <span id="modal-pelatihan-name"></span><br>
                    <strong>Peserta:</strong> <span id="modal-peserta-name"></span>
                </div>
                <p><small class="text-muted">Tindakan ini tidak dapat dibatalkan dan akan memungkinkan peserta mengakses evaluasi sebelum periode normal.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-warning" id="confirmForceOpen">Ya, Buka Paksa</button>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('styles')
<style>
.countdown-timer {
    min-width: 200px;
    font-size: 0.75rem;
    line-height: 1.2;
    padding: 8px 12px;
}
.countdown-display small {
    font-size: 0.7rem;
    opacity: 0.9;
}
.countdown-values {
    font-weight: bold;
    white-space: nowrap;
}
.countdown-values span {
    font-weight: bold;
    color: #fff;
}
@media (max-width: 768px) {
    .countdown-timer {
        min-width: 150px;
        font-size: 0.65rem;
        padding: 6px 8px;
    }
    .countdown-values {
        font-size: 0.7rem;
    }
}
</style>
@endpush

<script>
// Live countdown functionality
(function() {
    'use strict';
    
    console.log('Countdown script loaded');
    
    function updateCountdowns() {
        console.log('Running updateCountdowns at:', new Date().toLocaleTimeString());
        
        const timers = document.querySelectorAll('.countdown-timer[data-target]');
        console.log('Found countdown timers:', timers.length);
        
        timers.forEach(function(el, index) {
            const targetDateStr = el.getAttribute('data-target');
            const evaluasiId = el.getAttribute('data-evaluasi-id');
            
            if (!targetDateStr) return;
            
            const targetDate = new Date(targetDateStr).getTime();
            const now = new Date().getTime();
            const distance = targetDate - now;
            
            console.log(`Timer ${index} (ID: ${evaluasiId}): distance = ${distance}ms`);

            if (distance <= 0) {
                console.log(`Timer ${index}: Countdown finished!`);
                el.innerHTML = '<div style="text-align: center;"><small>Evaluasi Tersedia!</small></div>';
                el.classList.remove("bg-secondary");
                el.classList.add("bg-success");
                
                // Auto-reload page after countdown finishes to update action buttons
                setTimeout(function() {
                    console.log('Reloading page after countdown finished');
                    window.location.reload();
                }, 3000);
                return;
            }

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            // Try to update existing spans first
            const daysSpan = el.querySelector('.days');
            const hoursSpan = el.querySelector('.hours');
            const minutesSpan = el.querySelector('.minutes');
            const secondsSpan = el.querySelector('.seconds');

            if (daysSpan && hoursSpan && minutesSpan && secondsSpan) {
                daysSpan.textContent = days;
                hoursSpan.textContent = hours.toString().padStart(2, '0');
                minutesSpan.textContent = minutes.toString().padStart(2, '0');
                secondsSpan.textContent = seconds.toString().padStart(2, '0');
            } else {
                console.log(`Timer ${index}: Rebuilding content`);
                el.innerHTML = `
                    <div style="text-align: center; font-size: 0.75rem; line-height: 1.2;">
                        <div style="margin-bottom: 2px;">Dibuka dalam:</div>
                        <div style="font-weight: bold;">
                            <span class="days">${days}</span> hari
                            <span class="hours">${hours.toString().padStart(2, '0')}</span> jam<br>
                            <span class="minutes">${minutes.toString().padStart(2, '0')}</span> menit
                            <span class="seconds">${seconds.toString().padStart(2, '0')}</span> detik
                        </div>
                    </div>
                `;
            }
        });
    }

    function startCountdown() {
        console.log('Starting countdown...');
        updateCountdowns(); // Run immediately
        const intervalId = setInterval(updateCountdowns, 1000);
        console.log('Countdown interval started with ID:', intervalId);
        window.countdownInterval = intervalId;
    }

    // Force open functionality
    function initForceOpenHandlers() {
        // Test route button
        const testRouteBtn = document.getElementById('testRouteBtn');
        if (testRouteBtn) {
            testRouteBtn.addEventListener('click', function() {
                console.log('Testing route connection...');
                
                fetch('/test-force-open/999', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.text())
                .then(text => {
                    console.log('Test route response:', text);
                    try {
                        const data = JSON.parse(text);
                        alert('Route test successful: ' + data.message);
                    } catch(e) {
                        alert('Route test failed - got HTML response: ' + text.substring(0, 100));
                    }
                })
                .catch(error => {
                    console.error('Test route error:', error);
                    alert('Route test error: ' + error.message);
                });
            });
        }

        const forceOpenButtons = document.querySelectorAll('.force-open-btn');
        const modal = document.getElementById('forceOpenModal');
        const confirmButton = document.getElementById('confirmForceOpen');
        
        if (!modal || !confirmButton) return;
        
        let currentEvaluasiId = null;
        
        forceOpenButtons.forEach(button => {
            button.addEventListener('click', function() {
                currentEvaluasiId = this.getAttribute('data-evaluasi-id');
                const pelatihanName = this.getAttribute('data-pelatihan-name');
                const pesertaName = this.getAttribute('data-peserta-name');
                
                document.getElementById('modal-pelatihan-name').textContent = pelatihanName;
                document.getElementById('modal-peserta-name').textContent = pesertaName;
                
                const bootstrapModal = new bootstrap.Modal(modal);
                bootstrapModal.show();
            });
        });
        
        // Fixed force open functionality
        confirmButton.addEventListener('click', function() {
            if (!currentEvaluasiId) return;
            
            // Show loading
            this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...';
            this.disabled = true;
            
            // Construct the correct URL - note the route structure
            const url = `{{ url('/') }}/training/evaluasi-level-3/peserta/force-open/${currentEvaluasiId}`;
            
            console.log('Making request to:', url); // Debug log
            
            // Make AJAX request to force open
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json', // Important: specify we want JSON
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest' // Mark as AJAX request
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                
                // Check if response is ok
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                // Check content type
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    throw new Error('Response is not JSON');
                }
                
                return response.json();
            })
            .then(data => {
                console.log('Success response:', data);
                
                if (data.success) {
                    // Close modal and reload page
                    bootstrap.Modal.getInstance(modal).hide();
                    alert('Evaluasi berhasil dibuka paksa!');
                    window.location.reload();
                } else {
                    throw new Error(data.error || 'Terjadi kesalahan');
                }
            })
            .catch(error => {
                console.error('Force open error:', error);
                alert('Error: ' + error.message);
                this.innerHTML = 'Ya, Buka Paksa';
                this.disabled = false;
            });
        });
    }

    // Initialize everything when DOM is ready
    function initialize() {
        startCountdown();
        initForceOpenHandlers();
    }

    // Multiple ways to ensure the script runs
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialize);
    } else {
        initialize();
    }
    
    // Fallback timeout
    setTimeout(function() {
        if (!window.countdownInterval) {
            console.log('Starting countdown with timeout fallback');
            initialize();
        }
    }, 500);
})();
</script>