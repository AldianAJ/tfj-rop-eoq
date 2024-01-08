@extends('layouts.app')

@section('title')
    History Pengiriman Counter
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
        let mainTable = $('#datatable').DataTable({
            ordering: false,
            ajax: "{{ route('pengiriman-counter.history') }}",
            columns: [{
                    data: "pengiriman_counter_id"
                }, {
                    data: "permintaan_counter_id"
                },
                {
                    data: "name"
                },
                {
                    data: "tanggal_pengiriman",
                    render: function(data, type, row) {
                        if (row.status == 'Ditolak') {
                            return '-';
                        } else {
                            let date = new Date(data);
                            let tanggal_penjualan = new Intl.DateTimeFormat(['ban', 'id'], {
                                dateStyle: 'long',
                                timeZone: 'Asia/Jakarta'
                            }).format(date);
                            return tanggal_penjualan;
                        }
                    }
                },
                {
                    data: "tanggal_penerimaan",
                    render: function(data, type, row) {
                        if (data == null) {
                            return '-';
                        } else {
                            let date = new Date(data);
                            let tanggal_penjualan = new Intl.DateTimeFormat(['ban', 'id'], {
                                dateStyle: 'long',
                                timeZone: 'Asia/Jakarta'
                            }).format(date);
                            return tanggal_penjualan;
                        }

                    }
                },
                {
                    data: "status",
                    render: function(data, type, row) {
                        if (data != 'Ditolak') {
                            return '<span class="badge rounded-pill badge-soft-primary font-size-14">Selesai</span>';
                        } else {
                            return '<span class="badge rounded-pill badge-soft-danger font-size-14">Ditolak</span>';
                        }
                    }
                },
                {
                    data: "action"
                }
            ],
        });

        $('#datatable').on('click', '.btn-detail', function() {
            let selectedData = '';
            let slug = '';
            let indexRow = mainTable.rows().nodes().to$().index($(this).closest('tr'));
            selectedData = mainTable.row(indexRow).data();
            slug = selectedData.slug;
            $("#id-permintaan").text(selectedData.pengiriman_counter_id);
            $('#detail-datatable').DataTable().clear();
            $('#detail-datatable').DataTable().destroy();
            $('#detail-datatable').DataTable({
                ajax: {
                    "type": "POST",
                    "url": "{{ route('pengiriman-counter.detailHistory') }}",
                    "data": {
                        '_token': "{{ csrf_token() }}",
                        'slug': slug
                    }
                },
                lengthMenu: [5],
                columns: [{
                        data: "nama_barang",
                        name: "nama_barang"
                    },
                    {
                        data: "jumlah_pengiriman",
                        render: function(data, type, row) {
                            if (data == null) {
                                return '-';
                            } else {
                                return '<span class="badge rounded-pill badge-soft-danger font-size-14">Ditolak</span>';
                            }
                        }
                    },

                    {
                        data: "persetujuan",
                        name: "persetujuan"
                    },
                    {
                        data: "name",
                        render: function(data, type, row) {
                            if (data == null) {
                                return '-';
                            } else {
                                return '<span class="badge rounded-pill badge-soft-danger font-size-14">Ditolak</span>';
                            }
                        }
                    },
                    {
                        data: "catatan",
                        name: "catatan"
                    },
                ],
            });
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
            @if (session()->has('msg'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="mdi mdi-check-all me-2"></i>
                    {{ session('msg') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <div class="card">
                <div class="card-body">
                    <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>ID Pengiriman Counter</th>
                                <th>ID Permintaan Counter</th>
                                <th>Dikirim Ke</th>
                                <th>Tanggal Pengiriman</th>
                                <th>Tanggal Penerimaan</th>
                                <th>Status</th>
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
    <div class="modal modal-lg fade" id="detailModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Detail <span id="id-permintaan"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered dt-responsive nowrap w-100" id="detail-datatable">
                        <thead>
                            <tr>
                                <th>Nama Barang</th>
                                <th>Jumlah Pengiriman</th>
                                <th>Persetujuan</th>
                                <th>Dikirim dari</th>
                                <th>Catatan</th>
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
