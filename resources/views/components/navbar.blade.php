<nav
    class="layout-navbar container-xxl zindex-5 navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
    id="layout-navbar"
>
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
            <i class="bx bx-menu bx-sm"></i>
        </a>
    </div>

    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
        <!-- Search -->
        <form action="{{ url()->current() }}">
            <div class="navbar-nav align-items-center">
                <div class="nav-item d-flex align-items-center">
                    <i class="bx bx-search fs-4 lh-0"></i>
                    <input
                        type="text"
                        name="search"
                        value="{{ $search ?? '' }}"
                        class="form-control border-0 shadow-none"
                        placeholder="{{ __('navbar.search') }}"
                        aria-label="{{ __('navbar.search') }}"
                    />
                </div>
            </div>
        </form>
        <!-- /Search -->

        <ul class="navbar-nav flex-row align-items-center ms-auto">
            <!-- Notifikasi -->
            <li class="nav-item dropdown">
                <a class="nav-link position-relative d-flex align-items-center" href="#" id="notifDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bx bx-bell fs-4"></i>
                    <span class="ms-1">Notifikasi</span> <!-- tulisan tambahan -->
                    @if($unreadCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            {{ $unreadCount }}
                            <span class="visually-hidden">notifikasi baru</span>
                        </span>
                    @endif
                </a>
                <ul class="dropdown-menu dropdown-menu-end py-0" aria-labelledby="notifDropdown" style="width: 300px; max-height: 350px; overflow-y: auto;">
                    <li class="dropdown-header bg-light py-2 px-3 fw-semibold">Notifikasi</li>
                    @forelse($notifikasi as $note)
                        <li class="dropdown-item d-flex flex-column {{ !$note->dibaca ? 'bg-light' : '' }}">
                            <strong>{{ $note->judul }}</strong>
                            <span>{{ $note->pesan }}</span>
                            @if($note->link)
                                <a href="{{ $note->link }}" class="text-primary">Lihat Detail</a>
                            @endif
                            <small class="text-muted">{{ $note->created_at->diffForHumans() }}</small>
                        </li>
                    @empty
                        <li class="dropdown-item text-center text-muted">Belum ada notifikasi</li>
                    @endforelse
                </ul>
            </li>

            <!-- User -->
            <li class="nav-item navbar-dropdown dropdown-user dropdown ms-3">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="avatar avatar-online">
                        @php
                            $user = auth()->user();
                            $picture = $user->profile_picture;
                            $pictureUrl = $picture && !\Illuminate\Support\Str::startsWith($picture, 'http')
                                ? asset('storage/' . $picture)
                                : ($picture ?: 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random&size=128');
                        @endphp
                        <img src="{{ $pictureUrl }}" alt="Avatar" class="w-px-40 h-auto rounded-circle" />
                    </div>
                </a>

                <ul class="dropdown-menu dropdown-menu-end">
                    <!-- Header User -->
                    <li>
                        <a class="dropdown-item" href="{{ route('profile.show') }}">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar avatar-online">
                                        <img src="{{ $pictureUrl }}" alt="Avatar" class="w-px-40 h-auto rounded-circle" />
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <span class="fw-semibold d-block">{{ $user->name }}</span>
                                    <small class="text-muted text-capitalize">{{ $user->role }}</small>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li><div class="dropdown-divider"></div></li>

                    <!-- Profil -->
                    <li>
                        <a class="dropdown-item" href="{{ route('profile.show') }}">
                            <i class="bx bx-user me-2"></i> Profil
                        </a>
                    </li>

                    <!-- Logout -->
                    <li>
                        <form action="{{ route('logout') }}" method="post">
                            @csrf
                            <button class="dropdown-item cursor-pointer">
                                <i class="bx bx-power-off me-2"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </li>
            <!--/ User -->
        </ul>
    </div>
</nav>
