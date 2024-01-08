<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Admin\BarangCounter;
use Illuminate\Support\Str;

class BarangCounterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $datas = DB::table('barangs')->get();
        $counters = DB::table('counters')->get();

        foreach ($counters as $counter) {
            foreach ($datas as $data) {
                DB::beginTransaction();
                try {
                    $barang_counter_id = BarangCounter::generateBarangCounterId($counter->counter_id, $data->barang_id);
                    $barang_counter = new BarangCounter;
                    $barang_counter->barang_counter_id = $barang_counter_id;
                    $barang_counter->counter_id = $counter->counter_id;
                    $barang_counter->slug = Str::random(16);
                    $barang_counter->barang_id = $data->barang_id;
                    $barang_counter->save();
                    DB::commit();
                } catch (\Exception $ex) {
                    echo $ex->getMessage();
                    DB::rollBack();
                }
            }
        }
    }
}
