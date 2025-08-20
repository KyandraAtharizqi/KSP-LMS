
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
                        value="{{ old('search', request()->get('search')) }}"
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
                    <span class="ms-1">Notifikasi</span>
                    @if($unreadCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notifBadge">
                            {{ $unreadCount }}
                            <span class="visually-hidden">notifikasi baru</span>
                        </span>
                    @endif
                </a>
                <ul class="dropdown-menu dropdown-menu-end py-0" aria-labelledby="notifDropdown" style="width: 400px; max-height: 400px; overflow-y: auto;">
                    <li class="dropdown-header bg-light py-2 px-3 d-flex justify-content-between align-items-center">
                        <span>Notifikasi</span>
                        @if($unreadCount > 0)
                            <button onclick="markAllAsRead()" class="btn btn-sm btn-link p-0 text-primary">Tandai Sudah Dibaca</button>
                        @endif
                    </li>
                    @forelse($notifikasi as $note)
                        <li class="dropdown-item p-0 {{ !$note->dibaca ? 'bg-light' : '' }}" id="notif-{{ $note->id }}">
                            <div class="w-100 h-100">
                                @if($note->link)
                                    <a href="{{ $note->link }}" 
                                       class="text-decoration-none d-block p-2 h-100" 
                                       onclick="markAsRead({{ $note->id }}, event)"
                                       style="color: inherit;">
                                        <div class="d-flex align-items-start" style="min-width: 0;">
                                            <div class="flex-grow-1" style="min-width: 0;">
                                                <strong class="d-block text-truncate">{{ $note->judul }}</strong>
                                                <span class="d-block" style="white-space: normal; word-wrap: break-word;">{{ $note->pesan }}</span>
                                                <small class="text-muted d-block">{{ $note->created_at->diffForHumans() }}</small>
                                            </div>
                                            <div class="flex-shrink-0 ms-2">
                                                <button type="button" 
                                                        class="btn btn-link text-danger p-0" 
                                                        onclick="event.stopPropagation(); deleteNotification({{ $note->id }}, event);"
                                                        style="font-size: 1.2rem;">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </a>
                                @else
                                    <div class="p-2 h-100" style="cursor: pointer;" onclick="markAsRead({{ $note->id }})">
                                        <div class="d-flex align-items-start" style="min-width: 0;">
                                            <div class="flex-grow-1" style="min-width: 0;">
                                                <strong class="d-block text-truncate">{{ $note->judul }}</strong>
                                                <span class="d-block" style="white-space: normal; word-wrap: break-word;">{{ $note->pesan }}</span>
                                                <small class="text-muted d-block">{{ $note->created_at->diffForHumans() }}</small>
                                            </div>
                                            <div class="flex-shrink-0 ms-2">
                                                <button type="button" 
                                                        class="btn btn-link text-danger p-0" 
                                                        onclick="event.stopPropagation(); deleteNotification({{ $note->id }}, event);"
                                                        style="font-size: 1.2rem;">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </li>
                    @empty
                        <li class="dropdown-item text-center text-muted">Belum ada notifikasi</li>
                    @endforelse
                </ul>

                <script>
                    function markAsRead(id, event) {
                        // Dapatkan link sebelum preventDefault
                        const clickedElement = event ? event.target : null;
                        const link = clickedElement ? clickedElement.closest('a') : null;
                        const href = link ? link.getAttribute('href') : null;

                        // Prevent default hanya jika ada event
                        if (event) {
                            event.preventDefault();
                        }

                        fetch(`{{ url('notifikasi') }}/${id}/read`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                const notifElement = document.getElementById(`notif-${id}`);
                                if (notifElement) {
                                    notifElement.classList.remove('bg-light');
                                }
                                updateNotificationBadge(data.unreadCount);

                                // Redirect setelah notifikasi ditandai sebagai dibaca
                                if (href) {
                                    window.location.href = href;
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Error marking notification as read:', error);
                            // Jika terjadi error, tetap redirect
                            if (href) {
                                window.location.href = href;
                            }
                        });
                    }

                    function markAllAsRead() {
                        fetch('{{ url('notifikasi/read-all') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                document.querySelectorAll('[id^="notif-"]').forEach(el => {
                                    if (el) el.classList.remove('bg-light');
                                });
                                updateNotificationBadge(0);
                            }
                        });
                    }

                    function deleteNotification(id, event) {
                        if (event) {
                            event.preventDefault();
                            event.stopPropagation();
                        }

                        if (confirm('Apakah Anda yakin ingin menghapus notifikasi ini?')) {
                            const notifDeleteUrl = `{{ url('notifikasi') }}/${id}/delete`;
                            fetch(notifDeleteUrl, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json'
                                },
                                credentials: 'same-origin'
                            })
                            .then(res => res.json().catch(() => null))
                            .then(data => {
                                const notifElement = document.getElementById(`notif-${id}`);
                                if (data && data.success) {
                                    if (notifElement) notifElement.remove();
                                    updateNotificationBadge(data.unreadCount);
                                } else {
                                    // Jika gagal (termasuk 404), tetap hapus dari UI
                                    if (notifElement) notifElement.remove();
                                    updateNotificationBadge(0);
                                }
                                // Jika tidak ada notifikasi tersisa, tampilkan pesan
                                const notifList = document.querySelectorAll('[id^="notif-"]');
                                if (notifList.length === 0) {
                                    const dropdownMenu = document.querySelector('.dropdown-menu');
                                    const emptyMessage = document.createElement('li');
                                    emptyMessage.className = 'dropdown-item text-center text-muted';
                                    emptyMessage.textContent = 'Belum ada notifikasi';
                                    dropdownMenu.appendChild(emptyMessage);
                                }
                            })
                            .catch(error => {
                                // Jika error (termasuk 404), tetap hapus dari UI
                                const notifElement = document.getElementById(`notif-${id}`);
                                if (notifElement) notifElement.remove();
                                updateNotificationBadge(0);
                            });
                        }
                    }

                    function updateNotificationBadge(count) {
                        const badge = document.getElementById('notifBadge');
                        const header = document.querySelector('.dropdown-header');
                        const markAllBtn = header ? header.querySelector('button') : null;
                        const notifDropdown = document.querySelector('#notifDropdown');

                        if (count > 0) {
                            if (!badge && notifDropdown) {
                                const newBadge = document.createElement('span');
                                newBadge.id = 'notifBadge';
                                newBadge.className = 'position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger';
                                newBadge.innerHTML = `${count}<span class="visually-hidden">notifikasi baru</span>`;
                                notifDropdown.appendChild(newBadge);
                            } else if (badge) {
                                badge.innerHTML = `${count}<span class="visually-hidden">notifikasi baru</span>`;
                            }

                            // Pastikan tombol "Tandai Sudah Dibaca" ada
                            if (header && !markAllBtn) {
                                const newMarkAllBtn = document.createElement('button');
                                newMarkAllBtn.className = 'btn btn-sm btn-link p-0 text-primary';
                                newMarkAllBtn.textContent = 'Tandai Sudah Dibaca';
                                newMarkAllBtn.onclick = markAllAsRead;
                                header.appendChild(newMarkAllBtn);
                            }
                        } else {
                            if (badge) badge.remove();
                            if (markAllBtn) markAllBtn.remove();
                        }
                    }
                </script>
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
                        <a class="dropdown-item" href="javascript:void(0);" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="bx bx-power-off me-2"></i> Logout
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </li>
                </ul>
            </li>
            <!--/ User -->
        </ul>
    </div>
</nav>
