<header id="page-topbar">
    <div class="navbar-header">
        <div class="d-flex">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <a href="#" class="logo logo-dark" id="logoDarkMode">
                    <span class="logo-sm">
                        <img src="{{ asset('assets/images/logo-tfj-sm-light.png') }}" alt="" height="25">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('assets/images/logo-tfj-lg-light.png') }}" alt="" height="55"
                            style="margin-left: -2rem">
                    </span>
                </a>
                <a href="#" class="logo logo-light" id="logoLightMode">
                    <span class="logo-sm">
                        <img src="{{ asset('assets/images/logo-tfj-sm-dark.png') }}" alt="" height="25">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('assets/images/logo-tfj-lg-dark.png') }}" alt="" height="55"
                            style="margin-left: -2rem">
                    </span>
                </a>
            </div>
            <button type="button" class="btn btn-sm px-3 font-size-16 header-item waves-effect" id="vertical-menu-btn"
                title="Buka Menu Vertikal">
                <i class="fa fa-fw fa-bars"></i>
            </button>

        </div>

        <div class="d-flex">
            <div class="dropdown d-none d-lg-inline-block ms-1">
                <button type="button" class="btn header-item noti-icon waves-effect" data-bs-toggle="fullscreen"
                    aria-label="Toggle Fullscreen">
                    <i class="bx bx-fullscreen"></i>
                </button>
            </div>
            @if ($user->role == 'gudang')
                <div class="dropdown d-inline-block">
                    <button type="button" class="btn header-item noti-icon waves-effect"
                        id="page-header-notifications-dropdown" data-bs-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false">
                        <i class="bx bx-bell bx-tada"></i>
                        <span class="badge bg-danger rounded-pill" id="count-notif">1</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                        aria-labelledby="page-header-notifications-dropdown">
                        <div class="p-3 out-simple">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="m-0">Notifications</h6>
                                </div>
                            </div>
                        </div>
                        <div data-simplebar style="max-height: 300px;" class="parentNotif">
                            <a href="javascript:void(0);" class="text-reset notification-item">
                                <div class="d-flex">
                                    <div class="avatar-xs me-3">
                                        <span class="avatar-title bg-primary rounded-circle font-size-16">
                                            <i class="bx bx-cart"></i>
                                        </span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">Your order is placed</h6>
                                        <div class="font-size-12 text-muted">
                                            <p class="mb-1">If several languages coalesce the grammar</p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            @endif
            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img class="rounded-circle header-profile-user" src="{{ asset('assets/images/users/user22.png') }}"
                        alt="Header Avatar">
                    <span class="d-none d-xl-inline-block ms-1" key="t-henry">{{ $user->name }}</span>
                    <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    <div class="d-flex justify-content-center align-items-center">
                        <i class="bx bx-sun bx-sm" style="margin-right: 8px;"></i>
                        <div class="d-flex form-check form-switch" style="margin-bottom: 0; transform: scale(1.2);">
                            <input type="checkbox" class="form-check-input" id="darkMode">
                            <label class="form-check-label" for="darkMode"></label>
                        </div>
                        <i class="bx bx-moon bx-sm" style="margin-left: 0;"></i>
                    </div>
                    <div class="dropdown-divider">
                    </div>
                    <a class="dropdown-item text-danger fw-bold" href="{{ route('auth.logout') }}">
                        <i class="bx bx-log-out font-size-16 align-middle me-1 text-danger"></i>
                        <span key="t-logout">Logout</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>
