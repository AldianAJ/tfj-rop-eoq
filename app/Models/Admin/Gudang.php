<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Gudang extends Model
{
    use HasFactory;

    protected $table = 'gudangs';
    protected $primaryKey = 'gudang_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'gudang_id',
        'slug',
        'user_id'
    ];

    public static function generateGudangId()
    {
        // Ambil ID gudang terakhir
        $lastGudangId = DB::table('gudangs')->max('gudang_id');

        // Jika tidak ada data gudang sebelumnya, atur nomor urut menjadi 1
        $incrementGudangId = !empty($lastGudangId) ? ((int) substr($lastGudangId, 1) + 1) : 1;

        // Format nomor urut dengan tambahan nol di depan sesuai panjang
        $incrementGudangIdFormatted = str_pad($incrementGudangId, 5, '0', STR_PAD_LEFT);

        // Bangun ID gudang baru dengan menambahkan awalan "G"
        $newGudangId = "G" . $incrementGudangIdFormatted;
        return $newGudangId;
    }

}
