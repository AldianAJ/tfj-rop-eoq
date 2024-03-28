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
    <label for="supplier_id" class="col-md-2 col-form-label">ID Supplier</label>
    <div class="col-md-10">
        <select class="form-control" id="supplier_id" name="supplier_id">
            <option value="">-- Pilih ID Supplier --</option>
            @foreach ($suppliers as $supplier)
                <option value="{{ $supplier->supplier_id }}">{{ $supplier->supplier_id }}</option>
            @endforeach
        </select>
    </div>
</div>


<div class="mt-4 mb-3 row">
    <label for="quantity_satuan" class="col-md-2 col-form-label">Satuan</label>
    <div class="col-md-10">
        <input class="form-control" type="text" id="quantity_satuan" name="quantity_satuan"
            placeholder="Ketikkan Satuan" value="{{ !empty($barangs) ? $barangs->quantity_satuan : '' }}">
    </div>
</div>

<div class="mt-4 mb-3 row">
    <label for="konversi_quantity" class="col-md-2 col-form-label">Konversi Quantity</label>
    <div class="col-md-10">
        <input class="form-control" type="text" id="konversi_quantity" name="konversi_quantity"
            placeholder="Ketikkan Konversi Quantity" value="{{ !empty($barangs) ? $barangs->konversi_quantity : '' }}">
    </div>
</div>

<div class="mt-4 mb-3 row">
    <label for="konversi_satuan" class="col-md-2 col-form-label">Konversi Satuan</label>
    <div class="col-md-10">
        <input class="form-control" type="text" id="konversi_satuan" name="konversi_satuan"
            placeholder="Ketikkan Konversi Satuan" value="{{ !empty($barangs) ? $barangs->konversi_satuan : '' }}">
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
