<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Admin\Supplier;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $datas = file_get_contents('database/seeders/json/Supplier.json');
        $datas = json_decode($datas);
        foreach ($datas as $data) {
            DB::beginTransaction();
            try {
                $supplier_id = Supplier::generateSupplierId();
                $supplier = new Supplier;
                $supplier->supplier_id = $supplier_id;
                $supplier->slug = Str::random(16);
                $supplier->nama_supplier = $data->nama_supplier;
                $supplier->alamat = $data->alamat;
                $supplier->telp = $data->telp;
                $supplier->biaya_pemesanan = $data->biaya_pemesanan;
                $supplier->save();
                DB::commit();
            } catch (\Exception $ex) {
                echo $ex->getMessage();
                DB::rollBack();
    }
}
    }
}
