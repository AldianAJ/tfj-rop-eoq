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
        'permintaan_counter_id',
        'slug',
        'counter_id',
        'status',
        'tanggal_permintaan'
    ];

    public static function generatePermintaanCounterId($counter_id)
    {
        $now = Carbon::now();

        // Ambil ID permintaan terakhir untuk counter dan tahun ini
        $permintaan_counter_id = DB::table('permintaan_counters')
            ->whereYear('tanggal_permintaan', $now->year)
            ->where('counter_id', $counter_id)
            ->max('permintaan_counter_id');

        // Jika tidak ada data permintaan sebelumnya, atur nomor urut menjadi 1
        $incrementPermintaanCounterId = !empty($permintaan_counter_id) ? ((int) substr($permintaan_counter_id, 16) + 1) : 1;

        // Format nomor urut dengan tambahan nol di depan sesuai panjang
        $incrementPermintaanCounterIdFormatted = str_pad($incrementPermintaanCounterId, 5, '0', STR_PAD_LEFT);

        // Bangun ID permintaan baru dengan menambahkan awalan "PMC"
        $newPermintaanCounterId = "PMC." . $counter_id . "." . $now->year . "." . $incrementPermintaanCounterIdFormatted;
        return $newPermintaanCounterId;
    }

}
