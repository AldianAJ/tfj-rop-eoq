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
        'barang_id', 'slug', 'nama_barang', 'harga_barang', 'supplier_id', 'quantity_satuan', 'konversi_quantity', 'konversi_satuan', 'biaya_penyimpanan', 'rop'
    ];

    public static function generateBarangId()
    {
        $barang_id = DB::table('barangs')->max('barang_id');

        $barang_id = str_replace("B", "", $barang_id);
        $barang_id = (int) $barang_id + 1;

        $addZero = str_pad($barang_id, 5, "0", STR_PAD_LEFT);

        $newBarangId = "B{$addZero}";

        return $newBarangId;
    }
}
