@extends('layouts.app')

@section('title')
    Barang
@endsection

@push('before-app-style')
    <!-- DataTables -->
    <link href="assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />

    <!-- Responsive datatable examples -->
    <link href="assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css" rel="stylesheet"
        type="text/css" />
@endpush

@push('after-app-script')
    <script src="assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
    <!-- Responsive examples -->
    <script src="assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js"></script>

    <!-- Datatable init js -->
    <script src="assets/js/pages/datatables.init.js"></script>
    <script>
        const rupiah = (number) => {
            return new Intl.NumberFormat("id-ID", {
                style: "currency",
                currency: "IDR"
            }).format(number);
        }

        let barangDatatable = $('#datatable').DataTable({
            ajax: "{{ route('barang') }}",
            columns: [{
                    data: "barang_id",
                    name: "barang_id"
                },
                {
                    data: "nama_barang",
                    name: "nama_barang"
                },
                {
                    data: "harga_barang",
                    render: function(data, type, row) {
                        return rupiah(data);
                    }
                },
                {
                    data: "biaya_penyimpanan",
                    render: function(data, type, row) {
                        return rupiah(data);
                    }
                },
                {
                    data: "rop",
                    name: "rop"
                },
                {
                    data: "qty_total",
                    name: "qty_total"
                },
                {
                    data: "action",
                    name: "action"
                }
            ],
        });

        $('input[name=btnradio]').each(function(index, element) {
            // element == this
            $(this).on('change', function(e) {
                $('#datatable').DataTable().clear();
                $('#datatable').DataTable().destroy();
                const target = $(e.target).val();
                if (target == 'master') {
                    barangDatatable.columns(4).visible(true);
                    barangDatatable.columns(5).visible(true);
                    barangDatatable.columns(6).visible(true);
                    $(barangDatatable.columns(3).header()).text('Biaya Penyimpanan');
                    $('#datatable').DataTable({
                        ajax: "{{ route('barang') }}",
                        columns: [{
                                data: "barang_id",
                                name: "barang_id"
                            },
                            {
                                data: "nama_barang",
                                name: "nama_barang"
                            },
                            {
                                data: "harga_barang",
                                render: function(data, type, row) {
                                    return rupiah(data);
                                }
                            },
                            {
                                data: "biaya_penyimpanan",
                                render: function(data, type, row) {
                                    return rupiah(data);
                                }
                            },
                            {
                                data: "rop",
                                name: "rop"
                            },
                            {
                                data: "qty_total",
                                name: "qty_total"
                            },
                            {
                                data: "action",
                                name: "action"
                            }
                        ],
                    });
                } else {
                    barangDatatable.columns(4).visible(false);
                    barangDatatable.columns(5).visible(false);
                    barangDatatable.columns(6).visible(false);
                    $(barangDatatable.columns(3).header()).text('Quantity');
                    $('#datatable').DataTable({

                        ajax: {
                            "type": "GET",
                            "url": "{{ route('barang') }}",
                            "data": {
                                '_token': "{{ csrf_token() }}",
                                'target': target
                            }
                        },
                        columns: [{
                                data: "barang_id",
                                name: "barang_id"
                            },
                            {
                                data: "nama_barang",
                                name: "nama_barang"
                            },
                            {
                                data: "harga_barang",
                                render: function(data, type, row) {
                                    return rupiah(data);
                                }
                            },
                            {
                                data: "quantity",
                            },
                            // {
                            //     data: "rop",
                            //     name: "rop"
                            // },
                            // {
                            //     data: "qty_total",
                            //     name: "qty_total"
                            // },
                            // {
                            //     data: "action",
                            //     name: "action"
                            // }
                        ],
                    });
                }
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
                    <div class="row mb-4 mt-1">
                        <div class="col-8 d-flex justify-content-end">
                            <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                                <input type="radio" class="btn-check" name="btnradio" value="master" id="btnradio4"
                                    autocomplete="off" checked>
                                <label class="btn btn-outline-primary" for="btnradio4">Master Barang</label>

                                <input type="radio" class="btn-check" name="btnradio" value="gudang" id="btnradio5"
                                    autocomplete="off">
                                <label class="btn btn-outline-primary" for="btnradio5">Barang Gudang</label>

                                <input type="radio" class="btn-check" name="btnradio" value="counter" id="btnradio6"
                                    autocomplete="off">
                                <label class="btn btn-outline-primary" for="btnradio6">Barang Counter</label>
                            </div>
                        </div>

                        <div class="col">
                            <div class="d-flex justify-content-end">
                                <a href="{{ route('barang.create') }}" class="btn btn-primary waves-effect waves-light">
                                    <i class="bx bx-list-plus align-middle me-2 font-size-18"></i>Tambah
                                </a>
                            </div>
                        </div>
                    </div>

                    <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                        <thead>
                            <tr>
                                <th>ID Barang</th>
                                <th>Nama Barang</th>
                                <th>Harga Barang</th>
                                <th>Biaya Penyimpanan</th>
                                <th>ROP</th>
                                <th>Quantity Total</th>
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
