@extends('layouts.app')

@section('title')
    Transaksi Penjualan
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

        let mainTable = $('#datatable').DataTable({
            order: [
                [2, 'desc']
            ],
            columnDefs: [
                @if ($user->role == 'gudang' || $user->role == 'owner')
                    {
                        "visible": false,
                        "targets": 3
                    }, {
                        "visible": false,
                        "targets": 4
                    }, {
                        "visible": false,
                        "targets": 5
                    }
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
                console.log("la");
                let type = $(e.target).val();
                console.log(type);
                if (type == 'group') {
                    mainTable = $('#datatable').DataTable({
                        order: [
                            [2, 'desc']
                        ],
                        columnDefs: [
                            @if ($user->role == 'gudang' || $user->role == 'owner')
                                {
                                    "visible": false,
                                    "targets": 3
                                }, {
                                    "visible": false,
                                    "targets": 4
                                }, {
                                    "visible": false,
                                    "targets": 5
                                }
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
                        order: [
                            [2, 'desc']
                        ],
                        columnDefs: [
                            @if ($user->role == 'gudang' || $user->role == 'owner')
                                {
                                    "visible": true,
                                    "targets": 3
                                }, {
                                    "visible": true,
                                    "targets": 4
                                }, {
                                    "visible": true,
                                    "targets": 5
                                }, {
                                    "visible": false,
                                    "targets": 6
                                }, {
                                    "visible": false,
                                    "targets": 7
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
                            "type": "GET",
                            "url": "{{ route('penjualan') }}",
                            "data": {
                                '_token': "{{ csrf_token() }}",
                                'type': type
                            }
                        },
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
                                data: "nama_barang"
                            },
                            {
                                data: "quantity"
                            },
                            {
                                data: "subtotal",
                                render: function(data, type, row) {
                                    return rupiah(data);
                                }
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
                    <div class="row mb-1 mt-1">
                        <div class="col d-flex justify-content-center">
                            <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                                <input type="radio" class="btn-check" name="btnradio" value="group" id="btnradio4"
                                    autocomplete="off" checked>
                                <label class="btn btn-outline-primary" for="btnradio4">Kelompokkan</label>

                                <input type="radio" class="btn-check" name="btnradio" value="ungroup" id="btnradio5"
                                    autocomplete="off">
                                <label class="btn btn-outline-primary" for="btnradio5">Pisahkan</label>
                            </div>
                        </div>
                    </div>
                    <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                        <thead>
                            <tr>
                                <th>ID Penjualan</th>
                                @if ($user->role == 'gudang')
                                    <th>Nama Counter</th>
                                @endif
                                <th>Tanggal Penjualan</th>
                                <th>Nama Barang</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                                <th>Grand Total</th>
                                {{-- @if ($user->role == 'gudang') --}}
                                <th>Action</th>
                                {{-- @endif --}}
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
