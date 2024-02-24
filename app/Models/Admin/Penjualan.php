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
        $tahun = !empty($tahun) ? $tahun : $now->year;

        // Ambil ID penjualan terakhir untuk counter dan tahun tertentu
        $penjualan_id = DB::table('penjualans')
            ->whereYear('tanggal_penjualan', $tahun)
            ->where('counter_id', $counter_id)
            ->max('penjualan_id');

        // Jika tidak ada data penjualan sebelumnya, atur nomor urut menjadi 1
        $incrementPenjualanCounterId = !empty($penjualan_id) ? ((int) substr($penjualan_id, 16) + 1) : 1;

        // Format nomor urut dengan tambahan nol di depan sesuai panjang
        $incrementPenjualanCounterIdFormatted = str_pad($incrementPenjualanCounterId, 5, '0', STR_PAD_LEFT);

        // Bangun ID penjualan baru dengan menambahkan awalan "PNJ"
        $newPenjualanCounterId = "PNJ." . $counter_id . "." . $tahun . "." . $incrementPenjualanCounterIdFormatted;
        return $newPenjualanCounterId;
    }

}

