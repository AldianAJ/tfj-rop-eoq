<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PermintaanCounter extends Model
{
    use HasFactory;

    protected $table = 'permintaan_counters';
    protected $primaryKey = 'permintaan_counter_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'permintaan_counter_id', 'slug', 'counter_id', 'status', 'tanggal_permintaan'
    ];

    public static function generatePermintaanCounterId($counter_id)
    {
        $now = Carbon::now();
        $permintaan_counter_id = DB::table('permintaan_counters')
            ->whereYear('tanggal_permintaan', $now->year)
            ->where('counter_id', $counter_id)
            ->max('permintaan_counter_id');
        $addZero = '';
        $permintaan_counter_id = substr($permintaan_counter_id, 6, 6);
        $permintaan_counter_id = (int) $permintaan_counter_id + 1;
        $incrementPermintaanCounterId = $permintaan_counter_id;

        if (strlen($permintaan_counter_id) == 1) {
            $addZero = "0000";
        } elseif (strlen($permintaan_counter_id) == 2) {
            $addZero = "000";
        } elseif (strlen($permintaan_counter_id) == 3) {
            $addZero = "00";
        } elseif (strlen($permintaan_counter_id) == 4) {
            $addZero = "0";
        }

        $newPermintaanCounterId = "PMT." . $counter_id . "." . $now->year . "." . $addZero . $incrementPermintaanCounterId;
        return $newPermintaanCounterId;
    }
}
