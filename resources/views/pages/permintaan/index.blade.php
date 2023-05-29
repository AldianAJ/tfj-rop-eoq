@extends('layouts.app')

@section('title')
    Permintaan Counter
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

    <!-- Datatable init js -->
    <script src="{{ asset('assets/js/pages/datatables.init.js') }}"></script>
    <script>
        $('#datatable').DataTable({
            ajax: "{{ route('permintaan-counter') }}",
            columns: [{
                    data: "permintaan_id"
                },
                {
                    data: "name"
                },
                {
                    data: "status",
                    render: function(data, type, row) {
                        return '<span class="badge rounded-pill badge-soft-warning font-size-14">' + data +
                            '</span>';
                    }
                },
                {
                    data: "tanggal_permintaan",
                    render: function(data, type, row) {
                        let date = new Date(data);
                        let tanggal_permintaan = new Intl.DateTimeFormat(['ban', 'id'], {
                            dateStyle: 'long',
                            timeZone: 'Asia/Jakarta'
                        }).format(date);
                        return tanggal_permintaan;
                    }
                },
                {
                    data: "action"
                }
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
        @if (session()->has('msg'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="mdi mdi-check-all me-2"></i>
                {{ session('msg') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if ($user->role == 'counter')
                        <div class="d-flex justify-content-end mb-4">
                            <a href="{{ route('permintaan-counter.create') }}"
                                class="btn btn-primary waves-effect waves-light">
                                <i class="bx bx-list-plus align-middle me-2 font-size-18"></i>Tambah
                            </a>
                        </div>
                    @endif

                    <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                        <thead>
                            <tr>
                                <th>ID Permintaan</th>
                                <th>Nama Counter</th>
                                <th>Status</th>
                                <th>Tanggal Permintaan</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>

                </div>
            </div>
        </div> <!-- end col -->
    </div>
@endsection
