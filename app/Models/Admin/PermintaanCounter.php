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
        'permintaan_counter_id', 'slug', 'counter_id', 'status_permintaan', 'tanggal_permintaan'
    ];

    public static function generatePermintaanCounterId($counter_id)
    {
        $now = Carbon::now();

        $latest_permintaan_counter_id = DB::table('permintaan_counters')
            ->whereYear('tanggal_permintaan', $now->year)
            ->where('counter_id', $counter_id)
            ->max('permintaan_counter_id');

        $incrementPermintaanCounterId = (int) substr($latest_permintaan_counter_id, -5) + 1;

        $addZero = str_pad($incrementPermintaanCounterId, 5, "0", STR_PAD_LEFT);

        $newPermintaanCounterId = "PMC.{$counter_id}.{$now->year}.{$addZero}";

        return $newPermintaanCounterId;
    }
}
