<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Admin\Barang;
use Illuminate\Support\Str;
use PhpParser\Node\Stmt\TryCatch;

class BarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $datas = file_get_contents('database/seeders/json/Barang.json');
        $datas = json_decode($datas);
        foreach ($datas as $data) {
            DB::beginTransaction();
            try {
                $barang_id = Barang::generateBarangId();
                $barang = new Barang;
                $barang->barang_id = $barang_id;
                $barang->slug = Str::random(16);
                $barang->nama_barang = $data->nama_barang;
                $barang->harga_barang = $data->harga_barang;
                $barang->save();
                DB::commit();
            } catch (\Exception $ex) {
                echo $ex->getMessage();
                DB::rollBack();
            }
        }
    }
}
