@extends('layouts.app')

@section('title')
    Edit Supplier
@endsection

@push('after-app-script')
    <script>
        var toastElList = [].slice.call(document.querySelectorAll('.toast'));
        var toastList = toastElList.map(function(toastEl) {
            return new bootstrap.Toast(toastEl, {
                autohide: true,
                delay: 5000
            });
        });

        toastList.forEach(toast => {
            toast.show();
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
                        <li class="breadcrumb-item"><a href="">Supplier</a></li>
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
                    <form action="{{ route('supplier.update', ['slug' => $suppliers->slug]) }}" method="post">
                        @csrf
                        @include('pages.supplier.form')
                    </form>
                </div>
            </div>
            <div aria-live="polite" aria-atomic="true" class="d-flex justify-content-end align-items-end"
                style="position: fixed; bottom: 1rem; right: 1rem;">
                @if (session()->has('msg'))
                    <div class="toast align-items-center text-white bg-warning border-0" role="alert"
                        aria-live="assertive" aria-atomic="true">
                        <div class="toast-body">
                            <i class="mdi mdi-alert-outline me-2 text-white"></i>
                            <strong class="mr-auto">Warning</strong><br>
                            {{ session('msg') }}
                        </div>
                    </div>
                @endif
            </div>
        </div> <!-- end col -->
    </div>
@endsection
