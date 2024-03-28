<h4 class="card-title">Form @yield('title')</h4>

<div class="mt-4 mb-3 row">
    <label for="supplier_id" class="col-md-2 col-form-label">ID Supplier</label>
    <div class="col-md-10">
        <input class="form-control" type="text"
            value="{{ !empty($suppliers) ? $suppliers->supplier_id : $supplier_id }}" id="supplier_id" name="supplier_id"
            readonly>
    </div>
</div>

<div class="mt-4 mb-3 row">
    <label for="nama_supplier" class="col-md-2 col-form-label">Nama Supplier</label>
    <div class="col-md-10">
        <input class="form-control" type="text" id="nama_supplier"
            value="{{ !empty($suppliers) ? $suppliers->nama_supplier : '' }}" name="nama_supplier"
            placeholder="Ketikkan Nama Supplier">
    </div>
</div>

<div class="mt-4 mb-3 row">
    <label for="alamat" class="col-md-2 col-form-label">Alamat Counter</label>
    <div class="col-md-10">
        <input class="form-control" type="text" id="alamat"
            value="{{ !empty($suppliers) ? $suppliers->alamat : '' }}" name="alamat"
            placeholder="Ketikkan Alamat Supplier">
    </div>
</div>

<div class="mt-4 mb-3 row">
    <label for="telp" class="col-md-2 col-form-label">No. Telp Supplier</label>
    <div class="col-md-10">
        <input class="form-control" type="text" id="telp"
            value="{{ !empty($suppliers) ? $suppliers->telp : '' }}" name="telp"
            placeholder="Ketikkan Telp Supplier">
    </div>
</div>

<div class="mt-4 mb-3 row">
    <label for="biaya_pemesanan" class="col-md-2 col-form-label">Biaya Pemesanan</label>
    <div class="col-md-10">
        <input class="form-control" type="text" id="biaya_pemesanan"
            value="{{ !empty($suppliers) ? $suppliers->biaya_pemesanan : '' }}" name="biaya_pemesanan"
            placeholder="Ketikkan Biaya Pemesanan">
    </div>
</div>

<div class="mt-4 mb-3 row">
    <div class="col-md-2"></div>
    <div class="col-md-10">
        <a href="{{ route('supplier') }}" class="btn btn-secondary waves-effect waves-light">
            <i class="bx bx-caret-left align-middle me-2 font-size-18"></i>Kembali
        </a>
        <button type="submit" class="btn btn-primary waves-effect waves-light"><i
                class="bx bx bxs-save align-middle me-2 font-size-18"></i>Simpan</button>
    </div>
</div>
