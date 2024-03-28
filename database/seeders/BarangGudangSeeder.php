<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Admin\BarangGudang;

class BarangGudangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $datas = DB::table('barangs')->get();
        $datas = json_decode($datas);
        foreach ($datas as $data) {
            DB::beginTransaction();
            try {
                $barang_gudang_id = BarangGudang::generateBarangGudangId($data->barang_id);
                $barang_gudang = new BarangGudang;
                $barang_gudang->barang_gudang_id = $barang_gudang_id;
                $barang_gudang->slug = Str::random(16);
                $barang_gudang->barang_id = $data->barang_id;
                $barang_gudang->gudang_id = 'G00001';
                $barang_gudang->save();
                DB::commit();
            } catch (\Exception $ex) {
                echo $ex->getMessage();
                DB::rollBack();
            }
        }
    }
}
