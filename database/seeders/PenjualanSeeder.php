<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Admin\Penjualan;
use App\Models\Admin\DetailPenjualan;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class PenjualanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $datas = file_get_contents('database/seeders/json/Penjualan.json');
        $datas = json_decode($datas);

        DB::beginTransaction();
        try {
            foreach ($datas as $data) {
                $counter = DB::table('counters as c')
                    ->join('users as u', 'c.user_id', '=', 'u.user_id')
                    ->select('c.counter_id')
                    ->where('u.name', $data->counter)
                    ->first();

                $barang_counter = DB::table('barang_counters as bc')
                    ->join('barangs as b', 'bc.barang_id', '=', 'b.barang_id')
                    ->select('bc.barang_counter_id', 'b.harga_barang')
                    ->where(['b.nama_barang' => $data->nama_barang, 'bc.counter_id' => $counter->counter_id])
                    ->first();

                $tahun = Carbon::createFromFormat('d/m/Y', $data->tanggal_penjualan)->format('Y');
                $tanggal = Carbon::createFromFormat('d/m/Y', $data->tanggal_penjualan)->format('Y-m-d');
                $penjualan_id = Penjualan::generatePenjualanCounterId($counter->counter_id, $tahun);
                
                $penjualan_id = Penjualan::generatePenjualanCounterId($counter->counter_id, $tahun);
                $penjualan = new Penjualan;
                $penjualan->penjualan_id = $penjualan_id;
                $penjualan->slug = Str::random(16);
                $penjualan->counter_id = $counter->counter_id;
                $penjualan->grand_total = (int)$data->quantity * $barang_counter->harga_barang;
                $penjualan->tanggal_penjualan = $tanggal;
                $penjualan->save();
                $detail = new DetailPenjualan;
                $detail->penjualan_id = $penjualan_id;
                $detail->barang_counter_id = $barang_counter->barang_counter_id;
                $detail->quantity = (int)$data->quantity;
                $detail->subtotal = (int)$data->quantity * $barang_counter->harga_barang;
                $detail->save();
            }
            DB::commit();
        } catch (Exception $ex) {
            echo $ex->getMessage();
            DB::rollBack();
        }

    }
}
