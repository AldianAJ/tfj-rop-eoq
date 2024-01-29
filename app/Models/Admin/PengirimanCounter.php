<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class PengirimanCounter extends Model
{
    use HasFactory;

    protected $table = 'pengiriman_counters';
    protected $primaryKey = 'pengiriman_counter_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'pengiriman_counter_id', 'slug', 'permintaan_counter_id', 'tanggal_pengiriman', 'tanggal_penerimaan'
    ];

    public static function generatePengirimanCounterId($counter_id)
    {
        $now = Carbon::now();
        $pengiriman_counter_id = DB::table('pengiriman_counters as pg')
            ->join('permintaan_counters as pm', 'pg.permintaan_counter_id', '=', 'pm.permintaan_counter_id')
            ->whereYear('pg.tanggal_pengiriman', $now->year)
            ->where('pm.counter_id', $counter_id)
            ->max('pg.pengiriman_counter_id');
        $addZero = '';
        $pengiriman_counter_id = substr($pengiriman_counter_id, 16, 5);
        $pengiriman_counter_id = (int) $pengiriman_counter_id + 1;
        $incrementPengirimanCounterId = $pengiriman_counter_id;

        if (strlen($pengiriman_counter_id) == 1) {
            $addZero = "0000";
        } elseif (strlen($pengiriman_counter_id) == 2) {
            $addZero = "000";
        } elseif (strlen($pengiriman_counter_id) == 3) {
            $addZero = "00";
        } elseif (strlen($pengiriman_counter_id) == 4) {
            $addZero = "0";
        }

        $newPengirimanCounterId = "KRM." . $counter_id . "." . $now->year . "." . $addZero . $incrementPengirimanCounterId;
        return $newPengirimanCounterId;
    }
}
