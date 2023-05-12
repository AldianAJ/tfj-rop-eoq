<h4 class="card-title">Form @yield('title')</h4>
{{-- <p class="card-title-desc">Here are examples of <code>.form-control</code> applied to_counter
    each
    textual HTML5 <code>&lt;input&gt;</code> <code>type</code>.</p> --}}

<div class="mt-4 mb-3 row">
    <label for="id_counter" class="col-md-2 col-form-label">ID Counter</label>
    <div class="col-md-10">
        <input class="form-control" type="text" value="G0001" id="id_counter" readonly>
    </div>
</div>

<div class="mt-4 mb-3 row">
    <label for="nama_counter" class="col-md-2 col-form-label">Nama Counter</label>
    <div class="col-md-10">
        <input class="form-control" type="text" id="nama_counter" placeholder="Ketikkan Nama Counter">
    </div>
</div>

<div class="mt-4 mb-3 row">
    <label for="alamat_counter" class="col-md-2 col-form-label">Alamat Counter</label>
    <div class="col-md-10">
        <input class="form-control" type="text" id="alamat_counter" placeholder="Ketikkan Alamat Counter">
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
        <a href="{{ route('counter') }}" class="btn btn-secondary waves-effect waves-light">
            <i class="bx bx-caret-left align-middle me-2 font-size-18"></i>Kembali
        </a>
        <button type="submit" class="btn btn-primary waves-effect waves-light"><i
                class="bx bx bxs-save align-middle me-2 font-size-18"></i>Simpan</button>
    </div>
</div>
