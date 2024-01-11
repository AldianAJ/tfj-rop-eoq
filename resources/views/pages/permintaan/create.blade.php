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
        let mainTable = $('#datatable').DataTable({
            ajax: "{{ route('permintaan-counter.create') }}",
            lengthMenu: [5],
            columns: [{
                    data: "barang_id"
                },
                {
                    data: "nama_barang"
                },
                {
                    data: "action"
                }
            ],
        });

        let no = 1;
        let selectedData;
        let permintaan = [];
        let permintaanDatatable;

        const rupiah = (number) => {
            return new Intl.NumberFormat("id-ID", {
                style: "currency",
                currency: "IDR"
            }).format(number);
        }

        $('.alert').hide();

        function viewPermintaanDataTable(paramOne) {
            $('#datatable-permintaan').DataTable().clear();
            $('#datatable-permintaan').DataTable().destroy();
            if (paramOne.length > 0) {
                return $('#datatable-permintaan').DataTable({
                    lengthMenu: [5],
                    data: paramOne,
                    columns: [{
                            data: 'no'
                        },
                        {
                            data: 'nama_barang'
                        },
                        {
                            data: 'jumlah'
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
                return $('#datatable-permintaan').DataTable({
                    lengthMenu: [5],
                });
            }
        }

        $('#datatable').on('click', '.btn-add', function(e) {
            selectedData = '';
            let indexRow = mainTable.rows().nodes().to$().index($(this).closest('tr'));
            selectedData = mainTable.row(indexRow).data();
            $('#label-barang').text(selectedData.nama_barang);
            $('#jumlah_permintaan').val("");
        });

        function checkBarangAfterAddPermintaan(id_barang, jumlah_permintaan) {
            let found = false;
            if (permintaan.length > 0) {
                for (var key in permintaan) {
                    if (permintaan[key].id_barang == id_barang) {
                        permintaan[key].jumlah = Number(jumlah_permintaan);
                        found = true;
                        break;
                    }
                    found = false;
                }
                if (found == false) {
                    let permintaanTemp = {
                        "no": no++,
                        "id_barang": selectedData.barang_id,
                        "nama_barang": selectedData.nama_barang,
                        "harga_barang": selectedData.harga_barang,
                        "jumlah": Number(jumlah_permintaan),
                    }
                    permintaan.push(permintaanTemp);
                }
            } else {
                let permintaanTemp = {
                    "no": no++,
                    "id_barang": selectedData.barang_id,
                    "nama_barang": selectedData.nama_barang,
                    "harga_barang": selectedData.harga_barang,
                    "jumlah": Number(jumlah_permintaan),
                }
                permintaan.push(permintaanTemp);
            }
        }

        $('#btn-save-add').on('click', function(e) {
            let jumlah_permintaan = $('#jumlah_permintaan').val();
            let id_barang = selectedData.barang_id;
            checkBarangAfterAddPermintaan(id_barang, jumlah_permintaan);
            $('#jumlahModal').modal('toggle');
            permintaanDatatable = viewPermintaanDataTable(permintaan);
        });

        function changeNumberDelPermintaan() {
            no = 1;
            for (var key in permintaan) {
                permintaan[key].no = no;
                no++;
            }
        }

        $('#datatable-permintaan').on('click', '.btn-remove', function(e) {
            selectedPermintaan = '';
            let indexRow = permintaanDatatable.rows().nodes().to$().index($(this).closest('tr'));
            selectedPermintaan = permintaanDatatable.row(indexRow).data();
            permintaan.splice(indexRow, 1);
            changeNumberDelPermintaan();
            permintaanDatatable = viewPermintaanDataTable(permintaan);
        });

        $('.btn-close').on('click', function() {
            $('.alert').hide();
        });

        $('#save-permintaan').on('click', function() {
            if (permintaan.length > 0) {
                $.ajax({
                    type: "post",
                    url: "{{ route('permintaan-counter.store') }}",
                    data: {
                        '_token': "{{ csrf_token() }}",
                        'list_permintaans': JSON.stringify(permintaan)
                    },
                    success: function(response) {
                        no = 1;
                        permintaan = [];
                        $('.alert').show();
                        viewPermintaanDataTable(permintaan);
                        mainTable.clear();
                        mainTable.destroy();
                        mainTable = $('#datatable').DataTable({
                            ajax: "{{ route('permintaan-counter.create') }}",
                            lengthMenu: [5],
                            columns: [{
                                    data: "barang_id"
                                },
                                {
                                    data: "nama_barang"
                                },
                                {
                                    data: "action"
                                }
                            ],
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
        <div class="alert alert-success alert-dismissible" role="alert">
            <i class="mdi mdi-check-all me-2"></i>
            Permintaan berhasil disimpan
            <button type="button" class="btn-close" aria-label="Close"></button>
        </div>
        <div class="col-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">Data Barang</h4>
                    <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                        <thead>
                            <tr>
                                <th>ID Barang</th>
                                <th>Nama Barang</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">List Permintaan Barang</h4>
                    <div class="d-flex justify-content-end mb-4">
                        <button class="btn btn-primary waves-effect waves-light" id="save-permintaan">
                            <i class="bx bx-save align-middle me-2 font-size-18"></i>Simpan
                        </button>
                    </div>
                    <table id="datatable-permintaan" class="table table-bordered dt-responsive  nowrap w-100">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Barang</th>
                                <th>Jumlah</th>
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
    <div class="modal fade" id="jumlahModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Jumlah Permintaan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label for="nama_barang" class="form-label font-weight-bold" id="label-barang"></label>
                    <input class="form-control" type="text" value="" id="jumlah_permintaan"
                        placeholder="Masukkan Jumlah Permintaan">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" id="btn-save-add">Simpan</button>
                </div>
            </div>
        </div>
    </div>
@endsection
