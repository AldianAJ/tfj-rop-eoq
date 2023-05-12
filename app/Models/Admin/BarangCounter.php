<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangCounter extends Model
{
    use HasFactory;

    protected $table = 'barang_counters';
    protected $primaryKey = 'barang_counter_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'barang_counter_id', 'slug', 'counter_id', 'barang_id', 'stok_awal', 'stok_masuk', 'stok_keluar'
    ];

    public static function generateBarangCounterId($counter_id, $barang_id)
    {
        $newBarangCounterId = $counter_id . $barang_id;
        return $newBarangCounterId;
    }
}
