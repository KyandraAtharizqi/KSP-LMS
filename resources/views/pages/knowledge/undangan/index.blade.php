@extends('layout.main')

@section('title', 'Surat Undangan Knowledge Sharing')

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
                            <th>Lampiran</th>
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
                                @if ($item->lampiran)
                                    <a href="{{ asset('storage/' . $item->lampiran) }}" target="_blank">Lihat Lampiran</a>
                                @else
                                    <span class="text-muted">Tidak ada</span>
                                @endif
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
