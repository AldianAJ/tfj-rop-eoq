@extends('layouts.app')

@section('title')
    Kasir
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
        let mainTable = $('#datatable').DataTable({
            lengthMenu: [5, 10, 20, 50, 100],
            ajax: "{{ route('kasir') }}",
            "columnDefs": [{
                    className: "nama-barang",
                    "targets": [0]
                },
                {
                    className: "harga-barang",
                    "targets": [1]
                },
            ],
            order: [
                [1, 'desc']
            ],
            columns: [{
                    data: 'nama_barang',
                    name: 'Nama Barang'
                },
                {
                    data: 'harga_barang',
                    name: 'Harga Barang',
                    render: function(data, type, row) {
                        return rupiah(data);
                    },
                },
                {
                    data: 'quantity',
                    name: 'Quantity'
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
        let grandTotal = 0;
        let selectedData;
        let keranjang = [];
        let keranjangDatatable;

        const rupiah = (number) => {
            return new Intl.NumberFormat("id-ID", {
                style: "currency",
                currency: "IDR"
            }).format(number);
        }

        function viewKeranjangDataTable(paramOne) {
            $('#datatable-keranjang').DataTable().clear();
            $('#datatable-keranjang').DataTable().destroy();
            if (paramOne.length > 0) {
                console.log('onok');
                $('#grandTotal').text(rupiah(grandTotal));
                return $('#datatable-keranjang').DataTable({
                    lengthMenu: [5, 10, 20, 50, 100],
                    data: paramOne,
                    columns: [{
                            data: 'no'
                        },
                        {
                            data: 'nama_barang'
                        },
                        {
                            data: 'harga_barang',
                            render: function(data, type, row) {
                                return rupiah(data);
                            }
                        },
                        {
                            data: 'jumlah'
                        },
                        {
                            data: 'subtotal',
                            render: function(data, type, row) {
                                return rupiah(data);
                            }
                        },
                        {
                            data: 'id_barang',
                            render: function(data, type, row) {
                                return '<button class="btn btn-danger waves-effect waves-light btn-remove"><i class="bx bxs-trash align-middle font-size-18"></i></button>';
                            }
                        }
                    ],
                });
            } else {
                console.log('raonok');
                $('#grandTotal').text(rupiah(grandTotal));
                return $('#datatable-keranjang').DataTable({
                    lengthMenu: [5, 10, 20, 50, 100],
                });
            }
        }

        $('#datatable').on('click', '.btn-add', function(e) {
            selectedData = '';
            let indexRow = mainTable.rows().nodes().to$().index($(this).closest('tr'));
            selectedData = mainTable.row(indexRow).data();
            $('#label-barang').text(selectedData.nama_barang);
            $('#jumlah_pembelian').val("");
        });

        $('#btn-save-add').on('click', function(e) {
            // e.preventDefault();
            let jumlah_pembelian = $('#jumlah_pembelian').val();
            if (jumlah_pembelian > selectedData.quantity) {
                alert('Stok tidak cukup');
            } else {
                let keranjangTemp = {
                    "no": no++,
                    "id_barang": selectedData.barang_id,
                    "nama_barang": selectedData.nama_barang,
                    "harga_barang": selectedData.harga_barang,
                    "jumlah": Number(jumlah_pembelian),
                    "subtotal": (selectedData.harga_barang) * Number(jumlah_pembelian)
                }
                grandTotal += (selectedData.harga_barang) * Number(jumlah_pembelian);
                keranjang.push(keranjangTemp);
                $('#quantityModal').modal('toggle');
                keranjangDatatable = viewKeranjangDataTable(keranjang);
                // $('#grandTotal').text(rupiah(grandTotal));
            }
        });

        $('#datatable-keranjang').on('click', '.btn-remove', function(e) {
            selectedKeranjang = '';
            let indexRow = keranjangDatatable.rows().nodes().to$().index($(this).closest('tr'));
            selectedKeranjang = keranjangDatatable.row(indexRow).data();
            console.log(indexRow);
            console.log(selectedKeranjang);
            grandTotal -= selectedKeranjang.subtotal;
            keranjang.splice(indexRow, 1);
            keranjangDatatable = viewKeranjangDataTable(keranjang);
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
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">Barang Kasir</h4>
                    <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                        <thead>
                            <tr>
                                <th>Nama Barang</th>
                                <th class="col-md-2">Harga barang</th>
                                <th class="col-md-1">Quantity</th>
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
                    <h4 class="card-title mb-3">Keranjang Kasir</h4>
                    <div class="d-flex justify-content-end mb-4">
                        <a href="" class="btn btn-primary waves-effect waves-light">
                            <i class="bx bx-save align-middle me-2 font-size-18"></i>Simpan
                        </a>
                    </div>
                    <table id="datatable-keranjang" class="table table-bordered dt-responsive  nowrap w-100">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Barang</th>
                                <th class="col-md-2">Harga barang</th>
                                <th class="col-md-1">Quantity</th>
                                <th class="col-md-2">Subtotal</th>
                                <th class="col-md-1">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tr>
                            <th colspan="4" class="text-center font-size-18">Grand Total</th>
                            <th colspan="2" class="font-size-16"><span id="grandTotal">Rp 0,00</span></th>
                        </tr>
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
                    <h5 class="modal-title" id="exampleModalLabel">Jumlah Pembelian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label for="nama_barang" class="form-label font-weight-bold" id="label-barang"></label>
                    <input class="form-control" type="text" value="" id="jumlah_pembelian"
                        placeholder="Masukkan Jumlah Pembelian">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" id="btn-save-add">Simpan</button>
                </div>
            </div>
        </div>
    </div>
@endsection