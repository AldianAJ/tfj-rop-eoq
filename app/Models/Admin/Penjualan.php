<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class Penjualan extends Model
{
    use HasFactory;

    protected $table = 'penjualans';
    protected $primaryKey = 'penjualan_id';
    public $incrementing = false;
    protected $keyType = 'string';

    public static function generatePenjualanCounterId($counter_id, $tahun = null)
    {
        $now = Carbon::now();

        $tahun = (!empty($tahun) ? $tahun : $now->year);

        $penjualan_id = DB::table('penjualans')
            ->whereYear('tanggal_penjualan', $tahun)
            ->where('counter_id', $counter_id)
            ->max('penjualan_id');

        $penjualan_id = substr($penjualan_id, 16, 5);
        $penjualan_id = (int) $penjualan_id + 1;

        $addZero = str_pad($penjualan_id, 5, "0", STR_PAD_LEFT);

        $newPenjualanCounterId = "PNJ.{$counter_id}.{$tahun}.{$addZero}";

        return $newPenjualanCounterId;
    }
}
