@extends('layouts.app')

@section('title')
    Persetujuan
@endsection

@push('after-app-script')
    <script>
        $("#jumlah_pengiriman").keypress(function(evt) {
            var key = String.fromCharCode(evt.which);
            if (!(/[0-9]/.test(key))) {
                evt.preventDefault();
            }
        });

        $("#persetujuan").change(function(e) {
            let persetujuan = $("#persetujuan").val();
            if (persetujuan == 'Setuju') {
                $("#sumber").removeAttr("disabled");
                $("#jumlah_pengiriman").removeAttr("disabled");
                $("#catatan").attr("disabled", true);
            } else if (persetujuan == 'Tidak Setuju') {
                $("#catatan").removeAttr("disabled");
                $("#sumber").attr("disabled", true);
                $("#jumlah_pengiriman").attr("disabled", true);
                $("#jumlah_pengiriman").val("");
            } else {
                $("#sumber").attr("disabled", true);
                $("#jumlah_pengiriman").attr("disabled", true);
                $("#jumlah_pengiriman").val("");
                $("#catatan").attr("disabled", true);
                $("#catatan").val("");
            }
        });
    </script>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Persetujuan</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('barang') }}">Barang</a></li>
                        <li class="breadcrumb-item active">@yield('title')</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if (session()->has('msg'))
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <i class="mdi mdi-alert-outline me-2"></i>
                            {{ session('msg') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    <form action="{{ route('permintaan-counter.tmpPersetujuan') }}" method="post">
                        @csrf
                        <h4 class="card-title">Form @yield('title')</h4>
                        <input type="hidden" name="slug" value="{{ $permintaan->slug }}">
                        <div class="mt-4 mb-3 row">
                            <label for="id_barang" class="col-md-2 col-form-label">ID Barang</label>
                            <div class="col-md-10">
                                <input class="form-control" type="text" id="id_barang" name="barang_id"
                                    value="{{ $permintaan->barang_id }}" readonly>
                            </div>
                        </div>

                        <div class="mt-4 mb-3 row">
                            <label for="nama_barang" class="col-md-2 col-form-label">Nama Barang</label>
                            <div class="col-md-10">
                                <input class="form-control" type="text" name="nama_barang" id="nama_barang"
                                    value="{{ $permintaan->nama_barang }}" readonly>
                            </div>
                        </div>

                        <div class="mt-4 mb-3 row">
                            <label for="nama_barang" class="col-md-2 col-form-label">Nama Counter</label>
                            <div class="col-md-10">
                                <input class="form-control" type="text" name="nama_barang" id="nama_barang"
                                    value=" {{ $barang_counter->name }}" readonly>
                            </div>
                        </div>

                        <div class="mt-4 mb-3 row">
                            <label for="harga_barang" class="col-md-2 col-form-label">Stok Barang di
                                {{ $barang_counter->name }}</label>
                            <div class="col-md-10">
                                <input class="form-control" type="text" id="stok_sekarang" name="stok_sekarang"
                                    value="{{ $barang_counter->stok_masuk - $barang_counter->stok_keluar }}" readonly>
                            </div>
                        </div>

                        <div class="mt-4 mb-3 row">
                            <label for="harga_barang" class="col-md-2 col-form-label">Jumlah Permintaan</label>
                            <div class="col-md-10">
                                <input class="form-control" type="text" id="jumlah_permintaan" name="jumlah_pengiriman"
                                    value="{{ $permintaan->jumlah_permintaan }}" readonly>
                            </div>
                        </div>

                        <div class="mt-4 mb-3 row">
                            <label for="persetujuan" class="col-md-2 col-form-label">Persetujuan</label>
                            <div class="col-md-10">
                                <select name="persetujuan" id="persetujuan" class="form-select">
                                    <option value="">-- Pilih Persetujuan --</option>
                                    <option value="Setuju">Setuju</option>
                                    <option value="Tidak Setuju">Tidak Setuju</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-4 mb-3 row">
                            <label for="sumber" class="col-md-2 col-form-label">Sumber</label>
                            <div class="col-md-10">
                                <select name="sumber" id="sumber" class="form-select" disabled>
                                    <option value="">-- Pilih Sumber --</option>
                                    @foreach ($sumbers as $sumber)
                                        @if ($sumber->quantity > 0)
                                            <option value="{{ $sumber->sumber_id }}">{{ $sumber->nama }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- <div class="mt-4 mb-3 row">
                            <label for="harga_barang" class="col-md-2 col-form-label">Jumlah Pengiriman</label>
                            <div class="col-md-10">
                                <input class="form-control" type="text" id="jumlah_pengiriman" name="jumlah_pengiriman"
                                    disabled>
                            </div>
                        </div> --}}

                        <div class="mt-4 mb-3 row">
                            <label for="harga_barang" class="col-md-2 col-form-label">Catatan</label>
                            <div class="col-md-10">
                                <textarea class="form-control" id="catatan" name="catatan" cols="30" rows="10" disabled></textarea>
                            </div>

                            <div class="mt-4 mb-3 row">
                                <div class="col-md-2"></div>
                                <div class="col-md-10">
                                    <a href="{{ route('permintaan-counter.detailByGudang', ['slug' => $permintaan->slug]) }}"
                                        class="btn btn-secondary waves-effect waves-light">
                                        <i class="bx bx-caret-left align-middle me-2 font-size-18"></i>Kembali
                                    </a>
                                    <button type="submit" class="btn btn-primary waves-effect waves-light"><i
                                            class="bx bx bxs-save align-middle me-2 font-size-18"></i>Simpan</button>
                                </div>
                            </div>

                    </form>
                </div>
            </div>
        </div> <!-- end col -->
    </div>
@endsection
