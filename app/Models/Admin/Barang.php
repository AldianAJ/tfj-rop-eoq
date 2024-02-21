<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barangs';
    protected $primaryKey = 'barang_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'barang_id',
        'slug',
        'nama_barang',
        'harga_barang',
        'biaya_penyimpanan',
        'rop'
    ];

    public static function generateBarangId()
    {
        // Ambil ID barang terakhir
        $lastBarangId = DB::table('barangs')->max('barang_id');

        // Jika tidak ada data barang sebelumnya, atur nomor urut menjadi 1
        $incrementBarangId = !empty($lastBarangId) ? ((int) substr($lastBarangId, 1) + 1) : 1;

        // Format nomor urut dengan tambahan nol di depan sesuai panjang
        $incrementBarangIdFormatted = str_pad($incrementBarangId, 5, '0', STR_PAD_LEFT);

        // Bangun ID barang baru dengan menambahkan awalan "B"
        $newBarangId = "B" . $incrementBarangIdFormatted;
        return $newBarangId;
    }

}
