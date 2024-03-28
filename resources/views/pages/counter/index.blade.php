@extends('layouts.app')

@section('title')
    Counter
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
        $('#datatable').DataTable({
            ajax: "{{ route('counter') }}",
            columns: [{
                    data: "counter_id"
                },
                {
                    data: "name"
                },
                {
                    data: "address"
                },
                {
                    data: "username"
                },
                @if ($user->role == 'gudang')
                    {
                        data: "action"
                    }
                @endif
            ],
        });
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
                    @if ($user->role == 'gudang')
                        <div class="d-flex justify-content-end mb-4">
                            <a href="{{ route('counter.create') }}" class="btn btn-primary waves-effect waves-light">
                                <i class="bx bx-list-plus align-middle me-2 font-size-18"></i>Tambah
                            </a>
                        </div>
                    @endif

                    <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>ID Counter</th>
                                <th>Nama Counter</th>
                                <th>Alamat Counter</th>
                                <th>Username</th>
                                @if ($user->role == 'gudang')
                                    <th>Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
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
        </div> <!-- end col -->
    </div>
@endsection
