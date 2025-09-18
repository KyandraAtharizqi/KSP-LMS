<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('home') }}" class="app-brand-link">
            <img src="{{ asset('logo-black.png') }}" alt="{{ config('app.name') }}" width="35">
            <span class="app-brand-text demo text-black fw-bolder ms-2">{{ config('app.name') }}</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <!-- Home -->
        <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('home') ? 'active' : '' }}">
            <a href="{{ route('home') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="{{ __('menu.home') }}">{{ __('menu.home') }}</div>
            </a>
        </li>

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">{{ __('menu.header.main_menu') }}</span>
        </li>

        <!-- 👇 Training Menu -->
        <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('training.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-brain"></i>
                <div data-i18n="Training">Training</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('training.suratpengajuan.index') ? 'active' : '' }}">
                    <a href="{{ route('training.suratpengajuan.index') }}" class="menu-link">
                        <div data-i18n="Surat Pengajuan Pelatihan">Surat Pengajuan Pelatihan</div>
                    </a>
                </li>
                <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('training.surattugas.index') ? 'active' : '' }}">
                    <a href="{{ route('training.surattugas.index') }}" class="menu-link">
                        <div data-i18n="Surat Tugas Pelatihan">Surat Tugas Pelatihan</div>
                    </a>
                </li>
                <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('training.daftarhadirpelatihan.index') ? 'active' : '' }}">
                    <a href="{{ route('training.daftarhadirpelatihan.index') }}" class="menu-link">
                        <div data-i18n="Daftar Hadir">Daftar Hadir</div>
                    </a>
                </li>

                <!-- Evaluation Submenu -->
                <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('training.evaluasi-level-*') ? 'active open' : '' }}">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <div data-i18n="Evaluation">Evaluation</div>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('training.evaluasilevel1.index') ? 'active' : '' }}">
                            <a href="{{ route('training.evaluasilevel1.index') }}" class="menu-link">
                                <div data-i18n="Evaluation 1">Evaluasi Level 1 (Peserta)</div>
                            </a>
                        </li>
                        <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('training.evaluasilevel3.peserta.*') ? 'active' : '' }}">
                            <a href="{{ route('evaluasi-level-3.peserta.index') }}" class="menu-link">
                                <div data-i18n="Evaluation 3 Peserta">Evaluasi Level 3 (Peserta)</div>
                            </a>
                        </li>
                        <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('training.evaluasilevel3.atasan.*') ? 'active' : '' }}">
                            <a href="{{ route('evaluasi-level-3.atasan.index') }}" class="menu-link">
                                <div data-i18n="Evaluation Atasan">Evaluasi Level 3 (Atasan)</div>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- ✅ Pelatihan Log / Rekap (TOP LEVEL UNDER TRAINING) -->
                <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('training.pelatihanlog.*') ? 'active' : '' }}">
                    <a href="{{ route('training.pelatihanlog.index') }}" class="menu-link">
                        <div data-i18n="Pelatihan Log">Pelatihan Log / Rekap</div>
                    </a>
                </li>
            </ul>
        </li>


        <!-- 👇 Knowledge Sharing Menu -->
        <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('knowledge.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon bx bx-book-open"></i>
                <div data-i18n="Knowledge Sharing">Knowledge Sharing</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('knowledge.pengajuan.index') ? 'active' : '' }}">
                    <a href="{{ route('knowledge.pengajuan.index') }}" class="menu-link">
                        <div data-i18n="Pengajuan Knowledge Sharing">Pengajuan Knowledge Sharing</div>
                    </a>
                </li>
                <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('knowledge.undangan.index') ? 'active' : '' }}">
                    <a href="{{ route('knowledge.undangan.index') }}" class="menu-link">
                        <div data-i18n="Surat Undangan">Surat Undangan</div>
                    </a>
                </li>
                <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('knowledge.notadinas.index') ? 'active' : '' }}">
                    <a href="{{ route('knowledge.notadinas.index') }}" class="menu-link">
                        <div data-i18n="Nota Dinas">Nota Dinas</div>
                    </a>
                </li>
                <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('knowledge.daftarhadir.index') ? 'active' : '' }}">
                    <a href="{{ route('knowledge.daftarhadir.index') }}" class="menu-link">
                        <div data-i18n="Daftar Hadir">Daftar Hadir</div>
                    </a>
                </li>
                <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('knowledge.evaluasi.index') ? 'active' : '' }}">
                    <a href="{{ route('knowledge.evaluasi.index') }}" class="menu-link">
                        <div data-i18n="Evaluasi">Evaluasi</div>
                    </a>
                </li>
                <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('knowledge.log.index') ? 'active' : '' }}">
                    <a href="{{ route('knowledge.log.index') }}" class="menu-link">
                        <div data-i18n="Knowledge Log/Rekap">Knowledge Log / Rekap</div>
                    </a>
                </li>
            </ul>
        </li>

        <!-- 👇 E-Learning Menu -->
        <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('elearning.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-play-circle"></i>
                <div data-i18n="E-Learning">E-Learning</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('elearning.videos.index') ? 'active' : '' }}">
                    <a href="{{ route('elearning.videos.index') }}" class="menu-link">
                        <div data-i18n="Video Library">Video Library</div>
                    </a>
                </li>
                {{-- Future: quizzes, progress, etc --}}
            </ul>
        </li>


        <!-- Other Menu -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">{{ __('menu.header.other_menu') }}</span>
        </li>

        <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('gallery.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-images"></i>
                <div data-i18n="{{ __('menu.gallery.menu') }}">{{ __('menu.gallery.menu') }}</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('gallery.incoming') ? 'active' : '' }}">
                    <a href="{{ route('gallery.incoming') }}" class="menu-link">
                        <div data-i18n="{{ __('menu.gallery.incoming_letter') }}">{{ __('menu.gallery.incoming_letter') }}</div>
                    </a>
                </li>
                <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('gallery.outgoing') ? 'active' : '' }}">
                    <a href="{{ route('gallery.outgoing') }}" class="menu-link">
                        <div data-i18n="{{ __('menu.gallery.outgoing_letter') }}">{{ __('menu.gallery.outgoing_letter') }}</div>
                    </a>
                </li>
            </ul>
        </li>

        @php
            $canManageUsers = in_array(auth()->user()->role, ['admin', 'department_admin', 'division_admin']);
        @endphp

        @if($canManageUsers)
            <!-- User Management -->
            <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('user.*') ? 'active' : '' }}">
                <a href="{{ route('user.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-user-pin"></i>
                    <div data-i18n="{{ __('menu.users') }}">{{ __('menu.users') }}</div>
                </a>
            </li>
        @endif

        @if(auth()->user()->role == 'admin')
            <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('reference.*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-analyse"></i>
                    <div data-i18n="{{ __('menu.reference.menu') }}">{{ __('menu.reference.menu') }}</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('reference.classification.*') ? 'active' : '' }}">
                        <a href="{{ route('reference.classification.index') }}" class="menu-link">
                            <div data-i18n="{{ __('menu.reference.classification') }}">{{ __('menu.reference.classification') }}</div>
                        </a>
                    </li>
                    <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('reference.status.*') ? 'active' : '' }}">
                        <a href="{{ route('reference.status.index') }}" class="menu-link">
                            <div data-i18n="{{ __('menu.reference.status') }}">{{ __('menu.reference.status') }}</div>
                        </a>
                    </li>
                </ul>
            </li>
        @endif
    </ul>

    <div class="flex-grow-1"></div>

    <hr class="my-2 mx-0" />

    <div class="text-center border-bottom pb-3 mb-3 mt-2">
        <a href="{{ route('profile.show') }}">
            <div class="avatar avatar-online mx-auto">
                @php
                    $user = auth()->user();
                    $picture = $user->profile_picture;
                    $pictureUrl = $picture && !\Illuminate\Support\Str::startsWith($picture, 'http')
                        ? asset('storage/' . $picture)
                        : ($picture ?: 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random&size=128');
                @endphp
                <img src="{{ $pictureUrl }}" alt="Avatar" class="w-px-80 rounded-circle" />
            </div>
            <h6 class="mt-2 mb-0">{{ auth()->user()->name }}</h6>
            <small class="text-muted d-block">{{ auth()->user()->email }}</small>
        </a>
    </div>

    <div class="px-3 pb-3">
        <a href="{{ route('profile.show') }}" class="btn btn-outline-primary w-100 mb-2">
            <i class="bx bx-user me-2"></i> Profile
        </a>
        <form action="{{ route('logout') }}" method="POST" onsubmit="return confirm('Yakin ingin logout?');">
            @csrf
            <button type="submit" class="btn btn-outline-danger w-100">
                <i class="bx bx-power-off me-2"></i> Logout
            </button>
        </form>
    </div>
</aside>
