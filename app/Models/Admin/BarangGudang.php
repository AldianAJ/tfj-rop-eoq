<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BarangGudang extends Model
{
    use HasFactory;

    protected $table = 'barang_gudangs';
    protected $primaryKey = 'barang_gudang_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'barang_gudang_id', 'slug', 'barang_id', 'stok_awal', 'stok_masuk', 'stok_keluar'
    ];

    public static function generateBarangGudangId($barang_id)
    {
        $newBarangGudangId = "G00001" . $barang_id;
        return $newBarangGudangId;
    }
}
