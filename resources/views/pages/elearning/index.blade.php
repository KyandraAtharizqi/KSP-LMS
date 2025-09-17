@extends('layout.main')

@section('title', 'E-Learning Videos')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>E-Learning Video Library</h3>
        @if(Auth::user()->can('upload-videos'))
            <a href="{{ route('elearning.videos.create') }}" class="btn btn-primary">
                <i class="bx bx-upload"></i> Upload Video
            </a>
        @endif
    </div>

    <!-- Search & Filter -->
    <form method="GET" action="{{ route('elearning.videos.index') }}" class="mb-4 d-flex">
        <input type="text" name="search" class="form-control me-2" placeholder="Search videos..."
               value="{{ request('search') }}">
        <select name="topic" class="form-select me-2">
            <option value="">All Topics</option>
            @foreach($topics as $topic)
                <option value="{{ $topic }}" {{ request('topic') == $topic ? 'selected' : '' }}>
                    {{ $topic }}
                </option>
            @endforeach
        </select>
        <button class="btn btn-secondary">Filter</button>
    </form>

    <!-- Video Grid -->
    <div class="row">
        @forelse($videos as $video)
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <img src="{{ $video->thumbnail_url ?? asset('images/default-video.png') }}" 
                         class="card-img-top" alt="Video thumbnail">
                    <div class="card-body">
                        <h5 class="card-title">{{ $video->title }}</h5>
                        <span class="badge bg-info">{{ $video->topic ?? 'General' }}</span>
                        <p class="card-text mt-2">{{ Str::limit($video->description, 80) }}</p>
                        <a href="{{ route('elearning.videos.show', $video->id) }}" class="btn btn-sm btn-primary">
                            <i class="bx bx-play"></i> Watch
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <p>No videos available.</p>
        @endforelse
    </div>
</div>
@endsection
