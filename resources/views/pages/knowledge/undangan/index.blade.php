@extends('layout.main')

@section('title', 'Surat Undangan Knowledge Sharing')

@push('style')
<style>
.kirim-undangan-btn {
    cursor: pointer !important;
    pointer-events: auto !important;
    z-index: 999 !important;
    position: relative !important;
}

.kirim-undangan-btn:hover {
    background-color: #198754 !important;
    border-color: #198754 !important;
}
</style>
@endpush

@section('content')
<div class="page-heading">
    <h3>Surat Undangan Knowledge Sharing</h3>
</div>

<div class="page-content">
    <div class="card">
        <div class="card-header">
            <h5>Daftar Surat Undangan</h5>
        </div>

        <div class="card-body">
            @if($undangan->isEmpty())
                <p class="text-center text-muted">Belum ada surat undangan yang dapat ditampilkan.</p>
            @else
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Perihal</th>
                            <th>Dari</th>
                            <th>Kepada</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($undangan as $item)
                        <tr>
                            <td>{{ $item->perihal }}</td>
                            <td>
                                @php
                                    $kepadaUser = $users->firstWhere('name', $item->kepada);
                                @endphp
                                {{ $kepadaUser ? $kepadaUser->name . ' - ' . $kepadaUser->jabatan_full : $item->kepada }}
                            </td>
                            <td>
                                <ul class="mb-0">
                                    @foreach($item->peserta ?? [] as $p)
                                        @php
                                            $user = $users[$p['id']] ?? null;
                                        @endphp
                                        <li>
                                            {{ $user ? $user->name . ' - ' . $user->jabatan_full : ($p['name'] ?? $p['id']) }}
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td>{{ $item->tanggal_mulai->format('d M Y') }} - {{ $item->tanggal_selesai->format('d M Y') }}</td>
                            <td>
                                @php
                                    $statusUndangan = $item->status_undangan ?? 'draft';
                                @endphp
                                @if($statusUndangan === 'sent')
                                    <span class="badge bg-success">Terkirim</span>
                                @elseif($statusUndangan === 'draft')
                                    <span class="badge bg-warning">Draft</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($statusUndangan) }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-column gap-2">
                                    @php
                                        $currentUser = auth()->user();
                                        $isParticipant = collect($item->peserta ?? [])->pluck('id')->contains($currentUser->id);
                                        $canManage = $currentUser->role === 'admin' || 
                                                   $currentUser->id === $item->created_by || 
                                                   $currentUser->name === $item->kepada;
                                    @endphp
                                    
                                    @if($isParticipant && !$canManage)
                                        {{-- Peserta hanya bisa melihat undangan --}}
                                        <a href="{{ route('knowledge.undangan.show', $item->id) }}" class="btn btn-sm btn-primary">
                                            Lihat Undangan
                                        </a>
                                    @else
                                        {{-- Pengaju/Approver/Admin bisa edit dan kirim --}}
                                        @if ($item->lampiran)
                                            <a href="{{ asset('storage/' . $item->lampiran) }}" target="_blank" class="btn btn-sm btn-info">
                                                Lihat Lampiran
                                            </a>
                                        @endif
                                        
                                        @if($statusUndangan === 'draft')
                                            <a href="{{ route('knowledge.undangan.edit', $item->id) }}" class="btn btn-sm btn-warning">
                                                Ganti Tanggal
                                            </a>
                                            <button class="btn btn-sm btn-success kirim-undangan-btn" data-id="{{ $item->id }}" id="btn-kirim-{{ $item->id }}" type="button">
                                                Kirim ke Peserta
                                            </button>
                                        @elseif($statusUndangan === 'sent')
                                            <a href="{{ route('knowledge.undangan.show', $item->id) }}" class="btn btn-sm btn-primary">
                                                Lihat Undangan
                                            </a>
                                            <span class="text-success small">
                                                <i class="bi bi-check-circle"></i> Undangan telah dikirim
                                            </span>
                                        @else
                                            <a href="{{ route('knowledge.undangan.show', $item->id) }}" class="btn btn-sm btn-primary">
                                                Lihat Undangan
                                            </a>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
// Test function to ensure JavaScript is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded - JavaScript initialized');
    console.log('CSRF token available:', !!document.querySelector('meta[name="csrf-token"]'));
    
    // Add event listeners to all kirim undangan buttons
    const kirimButtons = document.querySelectorAll('.kirim-undangan-btn');
    console.log('Found kirim undangan buttons:', kirimButtons.length);
    
    kirimButtons.forEach(function(button, index) {
        console.log(`Setting up button ${index + 1}:`, button);
        
        // Add click event listener
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const id = this.getAttribute('data-id');
            console.log('Button clicked with ID:', id);
            kirimUndangan(id);
        });
        
        // Add visual feedback for debugging
        button.addEventListener('mouseenter', function() {
            console.log('Mouse entered button with ID:', this.getAttribute('data-id'));
        });
        
        button.addEventListener('mousedown', function() {
            console.log('Mouse down on button with ID:', this.getAttribute('data-id'));
        });
    });
});

function kirimUndangan(id) {
    console.log('kirimUndangan function called with ID:', id);
    
    if (!id) {
        console.error('No ID provided to kirimUndangan function');
        alert('Error: ID tidak ditemukan');
        return;
    }
    
    if (confirm('Apakah Anda yakin ingin mengirim undangan ini ke semua peserta?')) {
        console.log('User confirmed sending invitation');
        
        // Show loading
        const button = document.getElementById(`btn-kirim-${id}`);
        if (!button) {
            console.error('Button not found with ID:', `btn-kirim-${id}`);
            alert('Tombol tidak ditemukan');
            return;
        }
        
        console.log('Button found:', button);
        const originalText = button.innerHTML;
        button.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span>Mengirim...';
        button.disabled = true;
        
        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            console.error('CSRF token not found');
            alert('CSRF token tidak ditemukan. Silakan refresh halaman.');
            button.innerHTML = originalText;
            button.disabled = false;
            return;
        }
        
        // Construct URL using Laravel URL helper
        const baseUrl = '{{ url("knowledge/undangan") }}' + '/' + id + '/send';
        console.log('Making request to URL:', baseUrl);
        console.log('Using CSRF Token:', csrfToken.getAttribute('content'));
        
        fetch(baseUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        })
        .then(response => {
            console.log('Response received:', {
                status: response.status,
                statusText: response.statusText,
                url: response.url,
                ok: response.ok
            });
            
            if (!response.ok) {
                return response.text().then(text => {
                    console.error('Error response body:', text);
                    let errorMessage = `HTTP error! status: ${response.status}`;
                    try {
                        const errorData = JSON.parse(text);
                        if (errorData.message) {
                            errorMessage = errorData.message;
                        }
                    } catch (e) {
                        errorMessage = text || errorMessage;
                    }
                    throw new Error(errorMessage);
                });
            }
            return response.json();
        })
        .then(data => {
            console.log('Success response data:', data);
            if (data.success) {
                alert('Undangan berhasil dikirim!');
                
                // Update tampilan tanpa reload halaman
                updateUndanganStatus(id);
                
                // Reload halaman untuk memastikan data terbaru
                setTimeout(() => {
                    window.location.href = window.location.href;
                }, 2000);
            } else {
                alert('Gagal mengirim undangan: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            alert('Terjadi kesalahan saat mengirim undangan: ' + error.message);
        })
        .finally(() => {
            button.innerHTML = originalText;
            button.disabled = false;
        });
    } else {
        console.log('User cancelled sending invitation');
    }
}

function updateUndanganStatus(id) {
    console.log('Updating undangan status for ID:', id);
    
    // Find the table row for this undangan
    const button = document.getElementById(`btn-kirim-${id}`);
    if (!button) {
        console.error('Button not found for updating status');
        return;
    }
    
    const row = button.closest('tr');
    if (!row) {
        console.error('Table row not found for updating status');
        return;
    }
    
    // Update status badge
    const statusCell = row.cells[4]; // Status column (index 4)
    if (statusCell) {
        statusCell.innerHTML = '<span class="badge bg-success">Terkirim</span>';
    }
    
    // Update action buttons
    const actionCell = row.cells[5]; // Action column (index 5)
    if (actionCell) {
        const actionDiv = actionCell.querySelector('.d-flex');
        if (actionDiv) {
            // Replace buttons with "Lihat Undangan" and success message
            const newContent = `
                <a href="{{ route('knowledge.undangan.show', '') }}/${id}" class="btn btn-sm btn-primary">
                    Lihat Undangan
                </a>
                <span class="text-success small">
                    <i class="bi bi-check-circle"></i> Undangan telah dikirim
                </span>
            `;
            actionDiv.innerHTML = newContent;
        }
    }
    
    console.log('Status updated successfully for ID:', id);
}
</script>
@endpush
