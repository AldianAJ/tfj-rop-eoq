<h4 class="card-title">Form @yield('title')</h4>

<div class="mt-4 mb-3 row">
    <label for="id_barang" class="col-md-2 col-form-label">ID Barang</label>
    <div class="col-md-10">
        <input class="form-control" type="text"
            value="{{ !empty($barangs) ? $barangs->barang_id : '' }}{{ !empty($barang_id) ? $barang_id : '' }}"
            id="id_barang" readonly>
    </div>
</div>

<div class="mt-4 mb-3 row">
    <label for="nama_barang" class="col-md-2 col-form-label">Nama Barang</label>
    <div class="col-md-10">
        <input class="form-control" type="text" name="nama_barang" id="nama_barang"
            placeholder="Ketikkan Nama Barang" value="{{ !empty($barangs) ? $barangs->nama_barang : '' }}">
    </div>
</div>

<div class="mt-4 mb-3 row">
    <label for="harga_barang" class="col-md-2 col-form-label">Harga Barang</label>
    <div class="col-md-10">
        <input class="form-control" type="text" id="harga_barang" name="harga_barang"
            placeholder="Ketikkan Harga Barang" value="{{ !empty($barangs) ? $barangs->harga_barang : '' }}">
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
