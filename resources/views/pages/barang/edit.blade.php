@extends('layouts.app')

@section('title')
    Edit Barang
@endsection

@push('after-app-script')
    <script>
        $("#harga_barang").keypress(function(e) {
            var key = String.fromCharCode(e.which);

            if (!(/[0-9]/.test(key))) {
                e.preventDefault();
            }
        });
    </script>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">@yield('title')</h4>

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
                    <form action="{{ route('barang.update', ['slug' => $barangs->slug]) }}" method="post">
                        @csrf
                        @include('pages.barang.form')
                    </form>
                </div>
            </div>
        </div> <!-- end col -->
    </div>
@endsection
