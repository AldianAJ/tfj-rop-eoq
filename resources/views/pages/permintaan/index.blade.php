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
        let permintaanDatatable = $('#datatable').DataTable({
            ajax: "{{ route('permintaan-counter') }}",
            order: [
                [3, 'desc']
            ],
            columns: [{
                    data: "permintaan_id"
                },
                @if ($user->role == 'gudang')
                    {
                        data: "name"
                    },
                @endif {
                    data: "status_permintaan",
                    render: function(data, type, row) {
                        let html = '';
                        if (data == 'Delivered') {
                            html = '<span class="badge rounded-pill badge-soft-primary font-size-14">' +
                                data +
                                '</span>';
                        } else if (data == 'Processing') {
                            html = '<span class="badge rounded-pill badge-soft-warning font-size-14">' +
                                '<span class="bx bx-revision me-1"></span>' +
                                data +
                                '</span>';
                        }
                        return html;
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
            ]
        });

        $('#datatable').on('click', '.btn-detail', function() {
            let selectedData = '';
            let slug = '';
            let indexRow = permintaanDatatable.rows().nodes().to$().index($(this).closest('tr'));
            selectedData = permintaanDatatable.row(indexRow).data();
            console.log(selectedData);
            slug = selectedData.slug;
            if (selectedData.status == 'Dikirim') {
                window.location = '/pengiriman-counter/detail/' + slug;
            } else if (selectedData.status == 'Processing') {
                $("#id_permintaan").text(selectedData.permintaan_id);
                $('#detai-datatable').DataTable().clear().destroy();
                $('#detail-datatable').DataTable({
                    ajax: {
                        type: "POST",
                        url: "{{ route('permintaan-counter.detail') }}",
                        data: {
                            '_token': "{{ csrf_token() }}",
                            'slug': slug
                        }
                    },
                    lengthMenu: [5],
                    columns: [{
                            data: "nama",
                            name: "nama"
                        },
                        {
                            data: "quantity",
                            name: "quantity"
                        }
                    ]
                });
            }
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
                    @if ($user->role == 'counter')
                        <div class="d-flex justify-content-end mb-4">
                            <a href="{{ route('permintaan-counter.create') }}"
                                class="btn btn-primary waves-effect waves-light">
                                <i class="bx bx-list-plus align-middle me-2 font-size-18"></i>Tambah
                            </a>
                        </div>
                    @endif

                    <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                        <thead class="table-light">
                            <tr>
                                <th>ID Permintaan</th>
                                @if ($user->role == 'gudang')
                                    <th>Nama Counter</th>
                                @endif
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
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Detail <span id="id_permintaan"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered dt-responsive nowrap w-100" id="detail-datatable">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Barang</th>
                                <th>Jumlah Permintaan</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection
