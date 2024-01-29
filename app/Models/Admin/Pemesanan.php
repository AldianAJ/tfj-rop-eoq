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
        'pemesanan_id', 'slug', 'status_pemesanan', 'tanggal_pemesanan'
    ];

    public static function generatePemesananId()
    {
        $now = Carbon::now();
        $pemesanan_id = DB::table('pemesanans')->whereYear('tanggal_pemesanan', $now->year)->max('pemesanan_id');
        $addZero = '';
        $pemesanan_id = substr($pemesanan_id, 9, 6);
        $pemesanan_id = (int) $pemesanan_id + 1;
        $incrementPemesananId = $pemesanan_id;

        if (strlen($pemesanan_id) == 1) {
            $addZero = "0000";
        } elseif (strlen($pemesanan_id) == 2) {
            $addZero = "000";
        } elseif (strlen($pemesanan_id) == 3) {
            $addZero = "00";
        } elseif (strlen($pemesanan_id) == 4) {
            $addZero = "0";
        }

        $newPemesananId = "PMSN." . $now->year . "." . $addZero . $incrementPemesananId;
        return $newPemesananId;
    }
}
