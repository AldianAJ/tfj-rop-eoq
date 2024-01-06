@extends('layouts.app')

@section('title')
    Dashbooard
@endsection

@push('after-app-script')
    <!-- apexcharts -->
    <script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>

    <!-- dashboard blog init -->
    <script src="{{ asset('assets/js/pages/dashboard-blog.init.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <!-- Responsive examples -->
    <script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>

    <!-- Datatable init js -->
    <script src="{{ asset('assets/js/pages/datatables.init.js') }}"></script>
    <script>
        $('#datatable').DataTable({
            lengthMenu: [5, 10, 20, 50, 100],
            ordering: false,
            ajax: "{{ route('dashboard') }}",
            columns: [{
                    data: "barang_id"
                },
                {
                    data: "nama_barang"
                },
                {
                    data: "qty_total"
                },
                {
                    data: "rop"
                },
                {
                    data: "avg"
                },
                {
                    data: "avg",
                    render: function(data, type, row) {
                        if (parseInt(row.qty_total) <= parseInt(row.rop)) {
                            return '<div class="alert alert-danger d-flex align-items-center fw-bold" role="alert">Lakukan Pemesanan</div>';
                        } else if (parseInt(row.qty_total) <= parseInt(data)) {
                            return '<div class="alert alert-warning d-flex align-items-center fw-bold" role="alert">Segera Memesan</div>';
                        } else {
                            return '<div class="alert alert-success d-flex align-items-center fw-bold" role="alert">Aman</div>';
                        }
                    }
                }
            ],
        });
    </script>
@endpush

@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Dashboard</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboards</a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">

        <div class="col-xl">
            <div class="row">
                <div class="col-lg-3">
                    <div class="card mini-stats-wid">
                        <div class="card-body">

                            <div class="d-flex flex-wrap">
                                <div class="me-3">
                                    <p class="text-muted mb-2">Total Jenis Barang</p>
                                    <h5 class="mb-0">{{ $jumlah_jenis }}</h5>
                                </div>

                                {{-- <div class="avatar-sm ms-auto">
                                    <div class="avatar-title bg-light rounded-circle text-primary font-size-20">
                                        <i class="bx bxs-book-bookmark"></i>
                                    </div>
                                </div> --}}
                            </div>

                        </div>
                    </div>
                </div>

                <div class="col-lg-3">
                    <div class="card blog-stats-wid">
                        <div class="card-body">

                            <div class="d-flex flex-wrap">
                                <div class="me-3">
                                    <p class="text-muted mb-2">Total Transaksi ({{ $bulan_tahun }})</p>
                                    <h5 class="mb-0">{{ $total_transaksi }}</h5>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="card blog-stats-wid">
                        <div class="card-body">
                            <div class="d-flex flex-wrap">
                                <div class="me-3">
                                    <p class="text-muted mb-2">Total Pendapatan ({{ $bulan_tahun }})</p>
                                    <h5 class="mb-0">
                                        @php
                                            $hasil_rupiah = 'Rp ' . number_format($penjualan->total_pendapatan, 0, ',', '.');
                                            echo $hasil_rupiah;
                                        @endphp
                                    </h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="card blog-stats-wid">
                        <div class="card-body">
                            <div class="d-flex flex-wrap">
                                <div class="me-3">
                                    <p class="text-muted mb-2">Jumlah Counter</p>
                                    <h5 class="mb-0">{{ $jumlah_counter }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end row -->

            <div class="card">
                <div class="card-body">
                    <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                        <thead>
                            <tr>
                                <th>ID Barang</th>
                                <th>Nama Barang</th>
                                <th>Stok</th>
                                <th>ROP</th>
                                <th>Rata-Rata Penjualan</th>
                                <th>Label</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- end col -->

    </div>
    <!-- end row -->
@endsection
