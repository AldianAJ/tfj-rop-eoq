<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'suppliers';
    protected $primaryKey = 'supplier_id';
    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['supplier_id', 'slug', 'nama_supplier', 'alamat', 'telp', 'biaya_pemesanan'];

    public static function generateSupplierId()
    {
        $supplier_id = DB::table('suppliers')->max('supplier_id');

        $supplier_id = str_replace("S", "", $supplier_id);
        $supplier_id = (int) $supplier_id + 1;

        $addZero = str_pad($supplier_id, 5, "0", STR_PAD_LEFT);

        $newSupplierId = "S{$addZero}";

        return $newSupplierId;
    }
}
