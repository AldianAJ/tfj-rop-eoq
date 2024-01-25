@extends('layouts.app')

@section('title')
    Tambah Pemesanan Persediaan
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
            lengthMenu: [5, 10, 20, 50, 100],
            ajax: "{{ route('pemesanan.create') }}",
            "columnDefs": [{
                className: "nama-barang",
                "targets": [1]
            }],
            columns: [{
                    data: 'barang_id',
                    name: 'ID Barang'
                },
                {
                    data: 'nama_barang',
                    name: 'Nama Barang'
                },
                {
                    data: 'qty_total',
                    name: 'Quantity'
                },
                {
                    data: 'rop',
                    name: 'rop'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });

        let no = 1;
        let selectedData;
        let pemesanan = [];
        let pemesananDatatable;
        let biaya;

        const rupiah = (number) => {
            return new Intl.NumberFormat("id-ID", {
                style: "currency",
                currency: "IDR"
            }).format(number);
        }

        $('.alert-success').hide();
        $('.alert-warning').hide();
        $('.zero').hide();

        function viewPemesananDataTable(paramOne) {
            $('#datatable-pemesanan').DataTable().clear();
            $('#datatable-pemesanan').DataTable().destroy();
            if (paramOne.length > 0) {
                return $('#datatable-pemesanan').DataTable({
                    lengthMenu: [5, 10, 20, 50, 100],
                    data: paramOne,
                    columns: [{
                            data: 'no'
                        },
                        {
                            data: 'nama_barang'
                        },
                        {
                            data: 'eoq'
                        },
                        {
                            data: 'jumlah'
                        },
                        {
                            data: 'id_barang',
                            render: function(data, type, row) {
                                return '<button class="btn btn-success waves-effect waves-light btn-jumlah-pemesanan" data-bs-toggle="modal" data-bs-target="#quantityModal"><i class="bx bx-edit align-middle me-2 font-size-18"></i></button> <button class="btn btn-danger waves-effect waves-light btn-remove"><i class="bx bxs-trash align-middle font-size-18"></i></button>';
                            }
                        }
                    ],
                });
            } else {
                return $('#datatable-pemesanan').DataTable({
                    lengthMenu: [5, 10, 20, 50, 100],
                });
            }
        }

        function changeBarangAfterAddKasir(id_barang) {
            let found = false;
            if (pemesanan.length > 0) {
                for (var key in pemesanan) {
                    if (pemesanan[key].id_barang == id_barang) {
                        found = true;
                        break;
                    }
                    found = false;
                }
                if (found == false) {
                    let keranjangTemp = {
                        "no": no++,
                        "id_barang": selectedData.barang_id,
                        "nama_barang": selectedData.nama_barang,
                        "jumlah": 0,
                        "eoq": 0,
                    }
                    pemesanan.push(keranjangTemp);
                }
            } else {
                let keranjangTemp = {
                    "no": no++,
                    "id_barang": selectedData.barang_id,
                    "nama_barang": selectedData.nama_barang,
                    "jumlah": 0,
                    "eoq": 0,
                }
                pemesanan.push(keranjangTemp);
            }
        }

        $('#datatable').on('click', '.btn-add', function(e) {
            selectedData = '';
            let indexRow = mainTable.rows().nodes().to$().index($(this).closest('tr'));
            selectedData = mainTable.row(indexRow).data();
            let id_barang = selectedData.barang_id;
            changeBarangAfterAddKasir(id_barang);
            pemesananDatatable = viewPemesananDataTable(pemesanan);
        });

        $('#btn-save-add').on('click', function(e) {
            let id_barang = selectedKeranjang.id_barang;
            let jumlah_pemesanan = $('#jumlah_pemesanan').val();
            if (jumlah_pemesanan > 0) {
                for (var key in pemesanan) {
                    if (pemesanan[key].id_barang == id_barang) {
                        pemesanan[key].jumlah = jumlah_pemesanan;
                        break;
                    }
                }
                $('#quantityModal').modal('toggle');
                pemesananDatatable = viewPemesananDataTable(pemesanan);
            } else {
                $('#quantityModal').modal('toggle');
                $('.zero').show();
            }
        });

        function changeNumberDelKasir() {
            no = 1;
            for (var key in pemesanan) {
                pemesanan[key].no = no;
                no++;
            }
        }

        $("#biaya_pemesanan").keypress(function(evt) {
            var key = String.fromCharCode(evt.which);
            if (!(/[0-9]/.test(key))) {
                evt.preventDefault();
            }
        });

        $('#datatable-pemesanan').on('click', '.btn-jumlah-pemesanan', function(e) {
            $('#jumlah_pemesanan').val("");
            selectedKeranjang = '';
            let indexRow = pemesananDatatable.rows().nodes().to$().index($(this).closest('tr'));
            selectedKeranjang = pemesananDatatable.row(indexRow).data();
            $('#label-barang').text(selectedKeranjang.nama_barang);
        });

        $('#datatable-pemesanan').on('click', '.btn-remove', function(e) {
            selectedKeranjang = '';
            let indexRow = pemesananDatatable.rows().nodes().to$().index($(this).closest('tr'));
            selectedKeranjang = pemesananDatatable.row(indexRow).data();
            pemesanan.splice(indexRow, 1);
            changeNumberDelKasir();
            pemesananDatatable = viewPemesananDataTable(pemesanan);
        });

        $('.btn-close').on('click', function() {
            $('.alert').hide();
        });

        $('#save-transaction').on('click', function() {
            if (pemesanan.length > 0) {
                $.ajax({
                    type: "post",
                    url: "{{ route('pemesanan.store') }}",
                    data: {
                        '_token': "{{ csrf_token() }}",
                        'pemesanan': JSON.stringify(pemesanan),
                        'biaya': biaya
                    },
                    success: function(response) {
                        no = 1;
                        pemesanan = [];
                        $('.alert-success').show();
                        pemesananDatatable = viewPemesananDataTable(pemesanan);
                        $('#biaya').text('');
                    }
                });
            }
        });

        $('#btn-biaya-pemesanan').on('click', function() {
            $('#biaya_pemesanan').val('');
        });

        $('#btn-save-biaya').on('click', function() {
            biaya = $('#biaya_pemesanan').val();
            $('#biayaModal').modal('toggle');
            if (pemesanan.length > 0) {
                $('#biaya').text(rupiah(biaya));
                $.ajax({
                    type: "POST",
                    url: "{{ route('pemesanan.hitung') }}",
                    data: {
                        '_token': "{{ csrf_token() }}",
                        'pemesanan': JSON.stringify(pemesanan),
                        'biaya': biaya
                    },
                    dataType: "json",
                    success: function(response) {
                        // console.log(response.pemesanan);
                        pemesanan = [];
                        let data = response.pemesanan;
                        data.forEach(element => {
                            // console.log(element.no);
                            let pemesananTemp = {
                                "no": element.no,
                                "id_barang": element.id_barang,
                                "nama_barang": element.nama_barang,
                                "jumlah": element.eoq,
                                "eoq": element.eoq
                            };
                            pemesanan.push(pemesananTemp);
                            pemesananDatatable = viewPemesananDataTable(pemesanan);
                        });
                    }
                });
            }
        });
    </script>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">@yield('title')</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="alert alert-success alert-dismissible" role="alert">
            <i class="mdi mdi-check-all me-2"></i>
            Pemesanan persediaan berhasil disimpan
            <button type="button" class="btn-close" aria-label="Close"></button>
        </div>
        <div class="alert alert-warning alert-dismissible" role="alert">
            <i class="mdi mdi-alert-outline me-2"></i>
            Stok barang counter tidak cukup!!
            <button type="button" class="btn-close" aria-label="Close"></button>
        </div>
        <div class="alert alert-warning alert-dismissible zero" role="alert">
            <i class="mdi mdi-alert-outline me-2"></i>
            Jumlah pembelian harus diisi
            <button type="button" class="btn-close" aria-label="Close"></button>
        </div>
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">Barang</h4>

                    <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                        <thead>
                            <tr>
                                <th>ID Barang</th>
                                <th>Nama Barang</th>
                                <th class="col-md-1">Quantity</th>
                                <th class="col-md-1">ROP</th>
                                <th class="col-md-1">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">List Pemesanan Persediaan</h4>
                    <div class="row mb-3 mt-1">
                        <div class="col-3">
                            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#biayaModal"
                                id="btn-biaya-pemesanan">
                                <i class="bx bx-money align-middle me-2 font-size-18"></i>
                                Biaya Pemesanan
                            </button>
                        </div>
                        <div class="col d-flex justify-content-end">
                            <button class="btn btn-primary waves-effect waves-light" id="save-transaction">
                                <i class="bx bx-save align-middle me-2 font-size-18"></i>Simpan
                            </button>
                        </div>
                    </div>
                    <h5>Biaya Pemesanan : <span id="biaya"></span></h5>
                    <table id="datatable-pemesanan" class="table table-bordered dt-responsive  nowrap w-100">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Barang</th>
                                <th>EOQ</th>
                                <th>Jumlah Pemesanan</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="quantityModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Jumlah Pemesanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label for="nama_barang" class="form-label font-weight-bold" id="label-barang"></label>
                    <input class="form-control" type="text" value="" id="jumlah_pemesanan"
                        placeholder="Masukkan Jumlah Pemesanan">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" id="btn-save-add">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="biayaModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Biaya Pemesanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input class="form-control" type="text" value="" id="biaya_pemesanan"
                        placeholder="Masukkan Biaya Pemesanan">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" id="btn-save-biaya">Simpan</button>
                </div>
            </div>
        </div>
    </div>
@endsection
