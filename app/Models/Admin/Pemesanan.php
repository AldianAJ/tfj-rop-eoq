<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class Pemesanan extends Model
{
    use HasFactory;

    protected $table = 'pemesanans';
    protected $primaryKey = 'pemesanan_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'pemesanan_id',
        'slug',
        'status_pemesanan',
        'tanggal_pemesanan'
    ];

    public static function generatePemesananId()
    {
        $now = Carbon::now();
        $pemesanan_id = DB::table('pemesanans')->whereYear('tanggal_pemesanan', $now->year)->max('pemesanan_id');

        // Jika tidak ada pemesanan sebelumnya pada tahun ini, atur nomor urut menjadi 1
        $incrementPemesananId = !empty($pemesanan_id) ? ((int) substr($pemesanan_id, -6) + 1) : 1;

        // Format nomor urut dengan tambahan nol di depan sesuai panjang
        $incrementPemesananIdFormatted = str_pad($incrementPemesananId, 6, '0', STR_PAD_LEFT);

        // Bangun ID pemesanan baru
        $newPemesananId = "PMSN." . $now->year . "." . $incrementPemesananIdFormatted;
        return $newPemesananId;
    }

}
