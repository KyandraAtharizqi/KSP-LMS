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
                <!-- ✅ Daftar Hadir -->
                <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('training.daftarhadirpelatihan.index') ? 'active' : '' }}">
                    <a href="{{ route('training.daftarhadirpelatihan.index') }}" class="menu-link">
                        <div data-i18n="Daftar Hadir">Daftar Hadir</div>
                    </a>
                </li>

                <!-- ✅ New Evaluation Submenu -->
                <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('training.evaluation*') ? 'active open' : '' }}">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <div data-i18n="Evaluation">Evaluation</div>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('evaluasi_level_1.index') ? 'active' : '' }}">
                            <a href="{{ route('training.evaluasilevel1.index') }}" class="menu-link">
                                <div data-i18n="Evaluation 1">Evaluasi Level 1 (Peserta)</div>
                            </a>
                        </li>
                        <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('training.evaluation2.index') ? 'active' : '' }}">
                            <a href="{{ route('training.evaluation2.index') }}" class="menu-link">
                                <div data-i18n="Evaluation 2">Evaluation 2 (Peserta)</div>
                            </a>
                        </li>
                        <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('training.evaluation.atasan.index') ? 'active' : '' }}">
                            <a href="{{ route('training.evaluation.atasan.index') }}" class="menu-link">
                                <div data-i18n="Evaluation Atasan">Evaluation Atasan</div>
                            </a>
                        </li>
                        <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('training.evaluation.rekap.index') ? 'active' : '' }}">
                            <a href="{{ route('training.evaluation.rekap.index') }}" class="menu-link">
                                <div data-i18n="Rekapitulasi Jam">Rekapitulasi Jam</div>
                            </a>
                        </li>
                    </ul>
                </li>
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
</aside>
