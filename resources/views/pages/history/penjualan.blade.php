@extends('layouts.app')

@section('title')
    History Transaksi Penjualan
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

        $("#filter-month").css("visibility", "hidden");
        $("#btn-filter").css("visibility", "hidden");
        $("#wrap-btn-cetak").css("visibility", "hidden");

        let changeButton = (param) => {
            @if ($user->role == 'gudang' || $user->role == 'owner')
                if (param == 'ungroup') {
                    $("#filter-month").css("visibility", "visible");
                    $("#btn-filter").css("visibility", "visible");
                    $("#wrap-btn-cetak").css("visibility", "visible");
                } else {
                    $("#filter-month").css("visibility", "hidden");
                    $("#btn-filter").css("visibility", "hidden");
                    $("#wrap-btn-cetak").css("visibility", "hidden");
                }
            @endif
        }

        let mainTable = $('#datatable').DataTable({
            "ordering": false,
            columnDefs: [
                @if ($user->role == 'gudang' || $user->role == 'owner')
                    {
                        "visible": false,
                        "targets": 3
                    }, {
                        "visible": false,
                        "targets": 4
                    },
                @else
                    {
                        "visible": false,
                        "targets": 2
                    }, {
                        "visible": false,
                        "targets": 3
                    }, {
                        "visible": false,
                        "targets": 4
                    }
                @endif
            ],
            ajax: "{{ route('penjualan') }}",
            columns: [{
                    data: "penjualan_id"
                },
                @if ($user->role == 'gudang' || $user->role == 'owner')
                    {
                        data: "name"
                    },
                @endif {
                    data: "tanggal_penjualan",
                    render: function(data, type, row) {
                        let date = new Date(data);
                        let tanggal_penjualan = new Intl.DateTimeFormat(['ban', 'id'], {
                            dateStyle: 'long',
                            timeZone: 'Asia/Jakarta'
                        }).format(date);
                        return tanggal_penjualan;
                    }
                },
                {
                    data: null
                },
                {
                    data: null
                },
                {
                    data: "grand_total",
                    render: function(data, type, row) {
                        return rupiah(data);
                    }
                },
                {
                    data: "action"
                }
            ],
        });

        $('input[name=btnradio]').each(function(index, element) {
            // element == this
            $(this).on('change', function(e) {
                $('#datatable').DataTable().clear();
                $('#datatable').DataTable().destroy();
                // console.log("la");
                let type = $(e.target).val();
                console.log(type);
                if (type == 'group') {
                    let mainTable = $('#datatable').DataTable({
                        "ordering": false,
                        columnDefs: [
                            @if ($user->role == 'gudang' || $user->role == 'owner')
                                {
                                    "visible": false,
                                    "targets": 3
                                }, {
                                    "visible": false,
                                    "targets": 4
                                },
                            @else
                                {
                                    "visible": false,
                                    "targets": 2
                                }, {
                                    "visible": false,
                                    "targets": 3
                                }, {
                                    "visible": false,
                                    "targets": 4
                                }
                            @endif
                        ],
                        ajax: "{{ route('penjualan') }}",
                        columns: [{
                                data: "penjualan_id"
                            },
                            @if ($user->role == 'gudang' || $user->role == 'owner')
                                {
                                    data: "name"
                                },
                            @endif {
                                data: "tanggal_penjualan",
                                render: function(data, type, row) {
                                    let date = new Date(data);
                                    let tanggal_penjualan = new Intl.DateTimeFormat(['ban',
                                        'id'
                                    ], {
                                        dateStyle: 'long',
                                        timeZone: 'Asia/Jakarta'
                                    }).format(date);
                                    return tanggal_penjualan;
                                }
                            },
                            {
                                data: null
                            },
                            {
                                data: null
                            },
                            {
                                data: "grand_total",
                                render: function(data, type, row) {
                                    return rupiah(data);
                                }
                            },
                            {
                                data: "action"
                            }
                        ],
                    });
                } else if (type == 'ungroup') {
                    mainTable = $('#datatable').DataTable({
                        "ordering": false,
                        columnDefs: [
                            @if ($user->role == 'gudang' || $user->role == 'owner')
                                {
                                    "visible": false,
                                    "targets": 0
                                }, {
                                    "visible": false,
                                    "targets": 1
                                }, {
                                    "visible": true,
                                    "targets": 2
                                }, {
                                    "visible": true,
                                    "targets": 3
                                }, {
                                    "visible": true,
                                    "targets": 4
                                }, {
                                    "visible": false,
                                    "targets": 5
                                }, {
                                    "visible": false,
                                    "targets": 6
                                }
                            @else
                                {
                                    "visible": false,
                                    "targets": 0
                                }, {
                                    "visible": true,
                                    "targets": 1
                                }, {
                                    "visible": true,
                                    "targets": 2
                                }, {
                                    "visible": true,
                                    "targets": 3
                                }, {
                                    "visible": false,
                                    "targets": 4
                                }, {
                                    "visible": false,
                                    "targets": 5
                                },
                            @endif
                        ],
                        ajax: {
                            "type": "GET",
                            "url": "{{ route('penjualan') }}",
                            "data": {
                                '_token': "{{ csrf_token() }}",
                                'type': type
                            }
                        },
                        columns: [{
                                data: null
                            },
                            @if ($user->role == 'gudang' || $user->role == 'owner')
                                {
                                    data: null
                                },
                            @endif {
                                data: "tanggal_penjualan",
                                render: function(data, type, row) {
                                    let date = new Date(data);
                                    let options = {
                                        year: "numeric",
                                        month: "long",
                                        timeZone: 'Asia/Jakarta'
                                    };
                                    let tanggal_penjualan = new Intl.DateTimeFormat(['ban',
                                        'id'
                                    ], options).format(date);
                                    return tanggal_penjualan;
                                }
                            },
                            {
                                data: "nama_barang"
                            },
                            {
                                data: "total_penjualan"
                            },
                            {
                                data: null
                            },
                            {
                                data: null
                            }

                        ],
                    });
                }
                changeButton(type);
            });

        });

        $('#btn-filter').on('click', function() {
            let monthYear = $('#month-year').val();
            $('#datatable').DataTable().clear();
            $('#datatable').DataTable().destroy();
            mainTable = $('#datatable').DataTable({
                "ordering": false,
                columnDefs: [
                    @if ($user->role == 'gudang' || $user->role == 'owner')
                        {
                            "visible": false,
                            "targets": 0
                        }, {
                            "visible": false,
                            "targets": 1
                        }, {
                            "visible": true,
                            "targets": 2
                        }
                        , {
                            "visible": false,
                            "targets": 5
                        }, {
                            "visible": false,
                            "targets": 6
                        }
                    @else
                        {
                            "visible": true,
                            "targets": 2
                        }, {
                            "visible": true,
                            "targets": 3
                        }, {
                            "visible": true,
                            "targets": 4
                        }, {
                            "visible": false,
                            "targets": 5
                        }, {
                            "visible": false,
                            "targets": 6
                        },
                    @endif
                ],
                ajax: {
                    "type": "POST",
                    "url": "{{ route('penjualan.filter') }}",
                    "data": {
                        '_token': "{{ csrf_token() }}",
                        'bulan_tahun': monthYear
                    }
                },
                columns: [{
                        data: null
                    },
                    @if ($user->role == 'gudang' || $user->role == 'owner')
                        {
                            data: null
                        },
                    @endif {
                        data: "tanggal_penjualan",
                        render: function(data, type, row) {
                            let date = new Date(data);
                            let options = {
                                year: "numeric",
                                month: "long",
                                timeZone: 'Asia/Jakarta'
                            };
                            let tanggal_penjualan = new Intl.DateTimeFormat(['ban',
                                'id'
                            ], options).format(date);
                            return tanggal_penjualan;
                        }
                    },
                    {
                        data: "nama_barang"
                    },
                    {
                        data: "total_penjualan"
                    },
                    {
                        data: null
                    },
                    {
                        data: null
                    }
                ],
            });
            $('#bulan_tahun').val(monthYear);
        });

        $('#datatable').on('click', '.btn-detail', function() {
            let selectedData = '';
            let slug = '';
            let indexRow = mainTable.rows().nodes().to$().index($(this).closest('tr'));
            selectedData = mainTable.row(indexRow).data();
            slug = selectedData.slug;
            $("#id-penjualan").text(selectedData.penjualan_id);
            $('#detail-datatable').DataTable().clear();
            $('#detail-datatable').DataTable().destroy();
            $('#detail-datatable').DataTable({
                ajax: {
                    "type": "POST",
                    "url": "{{ route('penjualan.detail') }}",
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
                        data: "harga_barang",
                        render: function(data, type, row) {
                            return rupiah(data);
                        }
                    },
                    {
                        data: "quantity",
                        name: "quantity"
                    },
                    {
                        data: "subtotal",
                        render: function(data, type, row) {
                            return rupiah(data);
                        }
                    }
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
                    <div class="row mb-4 mt-1">
                        <div class="col-3">
                            <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                                <input type="radio" class="btn-check" name="btnradio" value="group" id="btnradio4"
                                    autocomplete="off" checked>
                                <label class="btn btn-outline-primary" for="btnradio4">Pertransaksi</label>

                                <input type="radio" class="btn-check" name="btnradio" value="ungroup" id="btnradio5"
                                    autocomplete="off">
                                <label class="btn btn-outline-primary" for="btnradio5">Perbarang</label>
                            </div>
                        </div>
                        <div class="col-5 d-flex justify-content-end" id="filter-month">
                            <label for="example-month-input" class="col-md-2 col-form-label">Bulan</label>
                            <div class="col-md-5">
                                <input class="form-control" type="month" value="2024-01" id="month-year">
                            </div>
                        </div>
                        <div class="col">
                            <button type="submit" class="btn btn-info" id="btn-filter">Filter</button>
                        </div>

                        <div class="col" id="wrap-btn-cetak">
                            <div class="d-flex justify-content-end">
                                <form action="{{ route('penjualan.exportPDF') }}" method="post">
                                    @csrf
                                    <input type="hidden" name="bulan_tahun" id="bulan_tahun">
                                    <button class="btn btn-primary waves-effect waves-light" id="btn-cetak">
                                        <i class="bx bxs-printer align-middle me-2 font-size-18"></i>Cetak PDF
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                        <thead>
                            <tr>
                                <th>ID Penjualan</th>
                                @if ($user->role == 'gudang' || $user->role == 'owner')
                                    <th>Nama Counter</th>
                                @endif
                                <th>Tanggal Penjualan</th>
                                <th>Nama Barang</th>
                                <th>Total Penjualan</th>
                                <th>Grand Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div> <!-- end col -->
    {{-- </div> --}}
    <div class="modal modal-lg fade" id="detailModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Detail <span id="id-penjualan"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered dt-responsive nowrap w-100" id="detail-datatable">
                        <thead>
                            <tr>
                                <th>Nama Barang</th>
                                <th>Harga</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
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
