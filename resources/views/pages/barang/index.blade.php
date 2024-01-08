@extends('layouts.app')

@section('title')
    Barang
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
                @if ($user->role == 'gudang' || $user->role == 'owner')
                    {
                        data: "biaya_penyimpanan",
                        render: function(data, type, row) {
                            return rupiah(data);
                        }
                    }, {
                        data: "rop",
                        name: "rop"
                    }, {
                        data: "qty_total",
                        name: "qty_total"
                    },
                    @if ($user->role == 'gudang' || $user->role == 'owner')
                        {
                            data: "action",
                            name: "action"
                        }
                    @endif
                @else
                    {
                        data: "quantity",
                        name: "quantity"
                    },
                @endif

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
                                name: "nama_barang",
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
                        ],
                    });
                }
            });
        });

        $("#biaya_penyimpanan").keypress(function(evt) {
            var key = String.fromCharCode(evt.which);
            if (!(/[0-9]/.test(key))) {
                evt.preventDefault();
            }
        });
        $('.alert-penyimpanan').hide();
        $('.alert-warning').hide();
        $('#btn-save').on('click', function() {
            let total_biaya = $('#biaya_penyimpanan').val();
            if (total_biaya != "") {
                $.ajax({
                    type: "post",
                    url: "{{ route('barang.biayapenyimpanan') }}",
                    data: {
                        '_token': "{{ csrf_token() }}",
                        'total_biaya': Number(total_biaya)
                    },
                    success: function(response) {
                        $('#biayaModal').modal('toggle');
                        $('.alert-penyimpanan').show();
                        $('#datatable').DataTable().clear();
                        $('#datatable').DataTable().destroy();
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
                        $('#biaya_penyimpanan').val("");
                        $('input[name=btnradio][value="master"]').prop('checked', true);
                    }
                });
            } else {
                $('#biayaModal').modal('toggle');
                $('.alert-warning').show();
                $('#biaya_penyimpanan').val("");
            }
        });

        $('#datatable').on('click', '.btn-detail', function() {
            let selectedData = '';
            let slug = '';
            let indexRow = barangDatatable.rows().nodes().to$().index($(this).closest('tr'));
            selectedData = barangDatatable.row(indexRow).data();
            slug = selectedData.slug;
            $("#nama-barang").text(selectedData.nama_barang);
            $('#detail-datatable').DataTable().clear();
            $('#detail-datatable').DataTable().destroy();
            $('#detail-datatable').DataTable({
                ajax: {
                    "type": "POST",
                    "url": "{{ route('barang.detail') }}",
                    "data": {
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
                ],
            });
        });

        $('.btn-close').on('click', function() {
            $('.alert').hide();
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
            <div class="alert alert-success alert-dismissible alert-penyimpanan" role="alert">
                <i class="mdi mdi-check-all me-2"></i>
                Biaya Penyimpanan berhasil disimpan
                <button type="button" class="btn-close" aria-label="Close"></button>
            </div>
            <div class="alert alert-warning alert-dismissible" role="alert">
                <i class="mdi mdi-alert-outline me-2"></i>
                Biaya penyimpanan tidak boleh kosong
                <button type="button" class="btn-close" aria-label="Close"></button>
            </div>
            <div class="card">
                <div class="card-body">
                    @if ($user->role == 'gudang')
                        <div class="row mb-4 mt-1">
                            <div class="col-3">
                                <button class="btn btn-pink" data-bs-toggle="modal" data-bs-target="#biayaModal">
                                    <i class="bx bx-money align-middle me-2 font-size-18"></i>
                                    Biaya Penyimpanan
                                </button>
                            </div>
                            <div class="col-5 d-flex justify-content-end">
                                <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                                    <input type="radio" class="btn-check" name="btnradio" value="master" id="btnradio4"
                                        autocomplete="off" checked>
                                    <label class="btn btn-outline-primary" for="btnradio4">Master Barang</label>

                                    <input type="radio" class="btn-check" name="btnradio" value="gudang" id="btnradio5"
                                        autocomplete="off">
                                    <label class="btn btn-outline-primary" for="btnradio5">Barang Gudang</label>
                                </div>
                            </div>

                            <div class="col">
                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('barang.create') }}"
                                        class="btn btn-primary waves-effect waves-light">
                                        <i class="bx bx-list-plus align-middle me-2 font-size-18"></i>Tambah
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif

                    <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                        <thead>
                            <tr>
                                <th>ID Barang</th>
                                <th>Nama Barang</th>
                                <th>Harga Barang</th>
                                @if ($user->role == 'gudang' || $user->role == 'owner')
                                    <th>Biaya Penyimpanan</th>
                                    <th>ROP</th>
                                    <th>Quantity Total</th>
                                    @if ($user->role == 'gudang' || $user->role == 'owner')
                                        <th>Action</th>
                                    @endif
                                @else
                                    <th>Quantity</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>

                </div>
            </div>
        </div> <!-- end col -->
    </div>

    <div class="modal fade" id="biayaModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Total Biaya Penyimpanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label for="nama_barang" class="form-label font-weight-bold">Total biaya penyimpanan akan dibagi total
                        stok barang agar menjadi biaya penyimpanan barang per unit</label>
                    <input class="form-control" type="text" value="" id="biaya_penyimpanan"
                        placeholder="Masukkan Total Biaya Penyimpanan">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" id="btn-save">Simpan</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Detail <span id="nama-barang"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered dt-responsive  nowrap w-100" id="detail-datatable">
                        <thead>
                            <tr>
                                <th>Sumber</th>
                                <th>Quantity</th>
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
