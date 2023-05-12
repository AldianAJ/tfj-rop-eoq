<h4 class="card-title">Form @yield('title')</h4>
{{-- <p class="card-title-desc">Here are examples of <code>.form-control</code> applied to
    each
    textual HTML5 <code>&lt;input&gt;</code> <code>type</code>.</p> --}}

<div class="mt-4 mb-3 row">
    <label for="id_gudang" class="col-md-2 col-form-label">ID Gudang</label>
    <div class="col-md-10">
        <input class="form-control" type="text" value="G0001" id="id_gudang" readonly>
    </div>
</div>

<div class="mt-4 mb-3 row">
    <label for="nama_gudang" class="col-md-2 col-form-label">Nama Gudang</label>
    <div class="col-md-10">
        <input class="form-control" type="text" id="nama_gudang" placeholder="Ketikkan Nama Gudang">
    </div>
</div>

<div class="mt-4 mb-3 row">
    <label for="alamat_gudang" class="col-md-2 col-form-label">Alamat Gudang</label>
    <div class="col-md-10">
        <input class="form-control" type="text" id="alamat_gudang" placeholder="Ketikkan Alamat Gudang">
    </div>
</div>

<div class="mt-4 mb-3 row">
    <label for="username" class="col-md-2 col-form-label">Username</label>
    <div class="col-md-10">
        <input class="form-control" type="text" id="username" placeholder="Ketikkan Username">
    </div>
</div>

<div class="mt-4 mb-3 row">
    <label for="password" class="col-md-2 col-form-label">Password</label>
    <div class="col-md-10">
        <input class="form-control" type="password" id="password" placeholder="Ketikkan Password">
    </div>
</div>

<div class="mt-4 mb-3 row">
    <div class="col-md-2"></div>
    <div class="col-md-10">
        <a href="{{ route('gudang') }}" class="btn btn-secondary waves-effect waves-light">
            <i class="bx bx-caret-left align-middle me-2 font-size-18"></i>Kembali
        </a>
        <button type="submit" class="btn btn-primary waves-effect waves-light"><i
                class="bx bx bxs-save align-middle me-2 font-size-18"></i>Simpan</button>
    </div>
</div>
