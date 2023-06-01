@extends('layouts.app')

@section('title')
    Detail Permintaan
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
        $('#datatable').DataTable(
            //     {
            //     ajax: "{{ route('gudang') }}",
            //     columns: [{
            //             data: "gudang_id"
            //         },
            //         {
            //             data: "name"
            //         },
            //         {
            //             data: "address"
            //         },
            //         {
            //             data: "username"
            //         },
            //         @if ($user->role == 'gudang')
            //             {
            //                 data: "action"
            //             }
            //         @endif
            //     ],
            // }
        );
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
                    <h4 class="card-title mb-3">Detail Permintaan {{ $permintaans->permintaan_counter_id }}</h4>
                    <div class="row mb-4 mt-2">
                        <div class="col d-flex justify-content-start ">
                            <a href="{{ route('permintaan-counter') }}" class="btn btn-secondary waves-effect waves-light">
                                <i class="bx bx-caret-left align-middle me-2 font-size-18"></i>Kembali
                            </a>
                        </div>
                        <div class="col d-flex justify-content-end ">
                            @if ($count_tmp == $count_detail)
                                <a href="{{ route('permintaan-counter.storePersetujuan', ['slug' => $permintaans->slug]) }}"
                                    class="btn btn-primary waves-effect waves-light">
                                    <i class="bx bx-save align-middle me-2 font-size-18"></i>Simpan
                                </a>
                            @endif
                        </div>
                    </div>
                    <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                        <thead>
                            <tr>
                                <th>Nama Barang</th>
                                <th>Jumlah Permintaan</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($details as $detail)
                                <tr>
                                    <td>
                                        {{ $detail->nama }}
                                    </td>
                                    <td>
                                        {{ $detail->quantity }}
                                    </td>
                                    <td>
                                        @if (!empty($temporary_persetujuan))
                                            @if (array_key_exists($detail->permintaan_counter_id . '/' . $detail->barang_id, $temporary_persetujuan))
                                                <span
                                                    class="badge rounded-pill badge-soft-success font-size-14">Selesai</span>
                                            @else
                                                <a href="{{ route('permintaan-counter.persetujuan', ['slug' => $detail->slug, 'id' => $detail->id]) }}"
                                                    class="btn btn-primary">Persetujuan</a>
                                            @endif
                                        @else
                                            <a href="{{ route('permintaan-counter.persetujuan', ['slug' => $detail->slug, 'id' => $detail->id]) }}"
                                                class="btn btn-primary">Persetujuan</a>
                                        @endif

                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div> <!-- end col -->
    </div>
@endsection
