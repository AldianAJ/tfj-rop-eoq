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
        'barang_id', 'slug', 'nama_barang', 'harga_barang', 'biaya_penyimpanan', 'rop'
    ];

    public static function generateBarangId()
    {
        $barang_id = DB::table('barangs')->max('barang_id');
        $addZero = '';
        $barang_id = str_replace("B", "", $barang_id);
        $barang_id = (int) $barang_id + 1;
        $incrementBarangId = $barang_id;

        if (strlen($barang_id) == 1) {
            $addZero = "0000";
        } elseif (strlen($barang_id) == 2) {
            $addZero = "000";
        } elseif (strlen($barang_id) == 3) {
            $addZero = "00";
        } elseif (strlen($barang_id) == 4) {
            $addZero = "0";
        }

        $newBarangId = "B" . $addZero . $incrementBarangId;
        return $newBarangId;
    }
}
