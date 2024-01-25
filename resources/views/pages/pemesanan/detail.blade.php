@extends('layouts.app')

@section('title')
    Detail Pemesanan
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
        let persetujuan;
        let pemesanan_id
        $('#datatable').DataTable({
            ordering: false
        });

        $('#save-persetujuan').on('click', function() {
            persetujuan = $("input[name='btnradio']:checked").val();
            pemesanan_id = $('#pemesanan_id').text();
            // console.log(pemesanan_id);
            $('#answer').text(persetujuan);
            $('#confirmModal').modal('toggle');

        });

        $('#btn-save').on('click', function() {
            $.ajax({
                type: "POST",
                url: "{{ route('pemesanan.persetujuan') }}",
                data: {
                    '_token': "{{ csrf_token() }}",
                    'persetujuan': persetujuan,
                    'pemesanan_id': pemesanan_id
                },
                dataType: "json",
                success: function(response) {
                    window.location = "{{ route('pemesanan') }}";
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
                    <h4 class="card-title mb-3">Detail Pemesanan <span
                            id="pemesanan_id">{{ $pemesanan->pemesanan_id }}</span></h4>
                    <div class="row mb-4 mt-1">
                        <div class="col-2">
                            <a href="{{ route('pemesanan') }}" class="btn btn-secondary waves-effect waves-light">
                                <i class="bx bx-caret-left align-middle me-2 font-size-18"></i>Kembali
                            </a>
                        </div>
                        @if ($user->role == 'owner' && $pemesanan->status_pemesanan == 'Menunggu Persetujuan')
                            <div class="col-5 d-flex justify-content-end">
                                <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                                    <input type="radio" class="btn-check" name="btnradio" value="Disetujui" id="btnradio4"
                                        autocomplete="off" checked>
                                    <label class="btn btn-outline-success" for="btnradio4">Disetujui</label>

                                    <input type="radio" class="btn-check" name="btnradio" value="Ditolak" id="btnradio5"
                                        autocomplete="off">
                                    <label class="btn btn-outline-danger" for="btnradio5">Ditolak</label>
                                </div>
                            </div>

                            <div class="col">
                                <div class="d-flex justify-content-end">
                                    <button class="btn btn-success waves-effect waves-light" id="save-persetujuan">
                                        <i class="bx bx-save align-middle me-2 font-size-18"></i>Simpan
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                    <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>Nama Barang</th>
                                <th>Stok Sekarang</th>
                                <th>ROP</th>
                                <th>EOQ</th>
                                <th>Jumlah Pemesanan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($detail_persetujuans as $detail_persetujuan)
                                <tr>
                                    <td>
                                        {{ $detail_persetujuan->nama_barang }}
                                    </td>
                                    <td>
                                        {{ $detail_persetujuan->stok }}
                                    </td>
                                    <td>
                                        {{ $detail_persetujuan->rop }}
                                    </td>
                                    <td>
                                        {{ $detail_persetujuan->eoq }}
                                    </td>
                                    <td>
                                        {{ $detail_persetujuan->jumlah_pemesanan }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </div> <!-- end col -->
    </div>
    <!-- Modal -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Konfirmasi Persetujuan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <span>Apakah anda yakin inbin menyimpan persetujuan dengan jawaban <span class="fw-bold"
                            id="answer"></span></span>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" id="btn-save">Simpan</button>
                </div>
            </div>
        </div>
    </div>
@endsection
