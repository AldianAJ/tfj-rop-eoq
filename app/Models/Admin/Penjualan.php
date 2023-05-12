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

    public static function generatePenjualanCounterId($counter_id)
    {
        $now = Carbon::now();
        $penjualan_id = DB::table('penjualans')
            ->whereYear('tanggal_penjualan', $now->year)
            ->where('counter_id', $counter_id)
            ->max('penjualan_id');
        $addZero = '';
        $penjualan_id = substr($penjualan_id, 6, 6);
        $penjualan_id = (int) $penjualan_id + 1;
        $incrementPenjualanCounterId = $penjualan_id;

        if (strlen($penjualan_id) == 1) {
            $addZero = "0000";
        } elseif (strlen($penjualan_id) == 2) {
            $addZero = "000";
        } elseif (strlen($penjualan_id) == 3) {
            $addZero = "00";
        } elseif (strlen($penjualan_id) == 4) {
            $addZero = "0";
        }

        $newPenjualanCounterId = "PNJ." . $counter_id . "." . $now->year . "." . $addZero . $incrementPenjualanCounterId;
        return $newPenjualanCounterId;
    }
}
