<h4 class="card-title">Form @yield('title')</h4>
{{-- <p class="card-title-desc">Here are examples of <code>.form-control</code> applied to
    each
    textual HTML5 <code>&lt;input&gt;</code> <code>type</code>.</p> --}}

<div class="mt-4 mb-3 row">
    <label for="id_barang" class="col-md-2 col-form-label">ID Barang</label>
    <div class="col-md-10">
        <input class="form-control" type="text" value="B0001" id="id_barang" readonly>
    </div>
</div>

<div class="mt-4 mb-3 row">
    <label for="nama_barang" class="col-md-2 col-form-label">Nama Barang</label>
    <div class="col-md-10">
        <input class="form-control" type="text" id="nama_barang" placeholder="Ketikkan Nama Barang">
    </div>
</div>

<div class="mt-4 mb-3 row">
    <label for="harga_barang" class="col-md-2 col-form-label">Harga Barang</label>
    <div class="col-md-10">
        <input class="form-control" type="text" id="harga_barang" placeholder="Ketikkan Harga Barang">
    </div>
</div>

<div class="mt-4 mb-3 row">
    <div class="col-md-2"></div>
    <div class="col-md-10">
        <a href="{{ route('barang') }}" class="btn btn-secondary waves-effect waves-light">
            <i class="bx bx-caret-left align-middle me-2 font-size-18"></i>Kembali
        </a>
        <button type="submit" class="btn btn-primary waves-effect waves-light"><i
                class="bx bx bxs-save align-middle me-2 font-size-18"></i>Simpan</button>
    </div>
</div>
