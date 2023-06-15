<div class="vertical-menu">

    <div data-simplebar class="h-100">

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                @if ($user->role == 'gudang' || $user->role == 'owner')
                    <li class="menu-title" key="t-menu">Menu</li>

                    <li>
                        <a href="{{ route('dashboard') }}">
                            <i class="bx bx-home-circle"></i>
                            <span key="t-dashboards">Dashboards</span>
                        </a>
                    </li>
                @endif

                <li class="menu-title" key="t-apps">Master Data</li>
                @if ($user->role == 'gudang' || $user->role == 'owner')
                    <li>
                        <a href="{{ route('gudang') }}" class="waves-effect">
                            <i class="bx bxs-building-house"></i>
                            <span key="t-gudang">Gudang</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('counter') }}" class="waves-effect">
                            <i class="bx bxs-store-alt"></i>
                            <span key="t-counter">Counter</span>
                        </a>
                    </li>
                @endif

                <li>
                    <a href="{{ route('barang') }}" class="waves-effect">
                        <i class="bx bxs-component"></i>
                        <span key="t-barang">Barang</span>
                    </a>
                </li>
                @if ($user->role == 'counter' || $user->role == 'gudang')
                    <li class="menu-title" key="t-pages">Utility</li>
                @endif


                @if ($user->role == 'counter')
                    <li>
                        <a href="{{ route('kasir') }}" class="waves-effect">
                            <i class="bx bxs-calculator"></i>
                            <span key="t-kasir">Kasir</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('pengiriman-counter.barangDiambil') }}" class="waves-effect">
                            <i class="bx bx-notepad"></i>
                            <span key="t-kasir">Barang Counter Diambil</span>
                        </a>
                    </li>
                @endif

                @if ($user->role == 'counter' || $user->role == 'gudang')
                    <li>
                        <a href="{{ route('permintaan-counter') }}" class="waves-effect">
                            <i class="bx bxs-duplicate"></i>
                            <span key="t-permintaan">Permintaan Counter</span>
                        </a>
                    </li>
                    {{-- <li>
                        <a href="chat.html" class="waves-effect">
                            <i class="bx bxs-truck"></i>
                            <span key="t-pengiriman">Pengiriman Counter</span>
                        </a>
                    </li> --}}
                @endif

                @if ($user->role == 'gudang' || $user->role == 'owner')
                    <li>
                        <a href="{{ route('pemesanan') }}" class="waves-effect">
                            <i class="bx bxs-package"></i>
                            <span key="t-pemesanan">Pemesanan Persediaan</span>
                        </a>
                    </li>
                @endif
                @if ($user->role == 'gudang')
                    <li>
                        <a href="{{ route('persediaan-masuk') }}" class="waves-effect">
                            <i class="bx bxs-widget"></i>
                            <span key="t-pemesanan">Persediaan Masuk</span>
                        </a>
                    </li>
                @endif

                <li class="menu-title" key="t-components">Riwayat</li>

                <li>
                    <a href="{{ route('penjualan') }}" class="waves-effect">
                        <i class="bx bxs-spreadsheet"></i>
                        <span key="t-kasir">Transaksi Penjualan</span>
                    </a>
                </li>

                <li>
                    <a href="chat.html" class="waves-effect">
                        <i class="bx bx-notepad"></i>
                        <span key="t-kasir">Permintaan Counter</span>
                    </a>
                </li>
                {{-- <li>
                    <a href="chat.html" class="waves-effect">
                        <i class="bx bx-book-content"></i>
                        <span key="t-kasir">Pengiriman Counter</span>
                    </a>
                </li> --}}
                @if ($user->role == 'gudang' || $user->role == 'owner')
                    <li>
                        <a href="chat.html" class="waves-effect">
                            <i class="bx bx-detail"></i>
                            <span key="t-kasir">Pemesanan Persediaan</span>
                        </a>
                    </li>

                    <li>
                        <a href="chat.html" class="waves-effect">
                            <i class="bx bx-task"></i>
                            <span key="t-kasir">Pengiriman Counter</span>
                        </a>
                    </li>
                @endif

            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
