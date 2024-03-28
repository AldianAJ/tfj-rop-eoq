@extends('layouts.app')

@section('title')
    Pengiriman
@endsection

@push('before-app-style')
    <!-- DataTables -->
    <link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}" rel="stylesheet"
        type="text/css" />

    <!-- Responsive datatable examples -->
    <link href="{{ asset('assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}" rel="stylesheet"
        type="text/css" />
@endpush

@push('after-app-script')
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <!-- Responsive examples -->
    <script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>

    <script>
        var toastElList = [].slice.call(document.querySelectorAll('.toast'));
        var toastList = toastElList.map(function(toastEl) {
            return new bootstrap.Toast(toastEl, {
                autohide: true,
                delay: 5000
            });
        });

        toastList.forEach(function(toast) {
            toast.show();
        });
    </script>
    <!-- Datatable init js -->
    <script src="{{ asset('assets/js/pages/datatables.init.js') }}"></script>
    <script>
        $('#datatable').DataTable();
    </script>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Data @yield('title')</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Tables</a></li>
                        <li class="breadcrumb-item active">Data @yield('title')</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">Detail Pengiriman {{ $data->pengiriman_counter_id }}</h4>
                    <div class="row mb-4 mt-2">
                        <div class="col d-flex justify-content-start ">
                            <a href="{{ route('permintaan-counter') }}" class="btn btn-secondary waves-effect waves-light">
                                <i class="bx bx-caret-left align-middle me-2 font-size-18"></i>Kembali
                            </a>
                        </div>
                        @if ($user->role == 'counter' && $permintaan->status == 'Shipped')
                            <div class="col d-flex justify-content-end">
                                <a href="{{ route('pengiriman-counter.penerimaan', ['slug' => $data->slug]) }}"
                                    class="btn btn-primary waves-effect waves-light">
                                    <i class="bx bxs-save align-middle me-2 font-size-18"></i>Simpan
                                </a>
                            </div>
                        @endif
                    </div>
                    <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Barang</th>
                                <th>Sumber</th>
                                <th>Jumlah Dikirim</th>
                                <th>Persetujuan</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pengirimans as $pengiriman)
                                <tr>
                                    <td>
                                        {{ $pengiriman->nama_barang }}
                                    </td>
                                    <td>
                                        {{ empty($pengiriman?->name) ? '-' : $pengiriman->name }}
                                    </td>
                                    <td>
                                        {{ empty($pengiriman?->jumlah_pengiriman) ? '-' : $pengiriman->jumlah_pengiriman }}
                                    </td>
                                    <td>
                                        {{ $pengiriman->persetujuan }}
                                    </td>
                                    <td>
                                        {{ $pengiriman->catatan }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div aria-live="polite" aria-atomic="true" class="d-flex justify-content-end align-items-end"
                style="position: fixed; bottom: 1rem; right: 1rem;">
                @if (session()->has('msg'))
                    <div aria-live="polite" aria-atomic="true" class="d-flex justify-content-end align-items-end"
                        style="position: fixed; bottom: 1rem; right: 1rem;">
                        <div class="toast align-items-center text-white bg-success border-0" role="alert"
                            aria-live="assertive" aria-atomic="true">
                            <div class="toast-body">
                                <i class="mdi mdi-check-all me-2 text-white"></i>
                                <strong class="mr-auto">Success</strong><br>
                                {{ session('msg') }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div> <!-- end col -->
    </div>
@endsection
