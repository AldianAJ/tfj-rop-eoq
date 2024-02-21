<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PersediaanMasuk extends Model
{
    use HasFactory;

    protected $table = 'persediaan_masuks';
    protected $primaryKey = 'persediaan_masuk_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'persediaan_masuk_id',
        'slug',
        'pemesanan_id',
        'tanggal_persediaan_masuk'
    ];

    public static function generatePersediaanMasukId()
    {
        $now = Carbon::now();

        // Ambil ID persediaan masuk terakhir untuk tahun ini
        $persediaan_masuk_id = DB::table('persediaan_masuks')
            ->whereYear('tanggal_persediaan_masuk', $now->year)
            ->max('persediaan_masuk_id');

        // Jika tidak ada data persediaan masuk sebelumnya, atur nomor urut menjadi 1
        $incrementPersediaanMasukId = !empty($persediaan_masuk_id) ? ((int) substr($persediaan_masuk_id, 9) + 1) : 1;

        // Format nomor urut dengan tambahan nol di depan sesuai panjang
        $incrementPersediaanMasukIdFormatted = str_pad($incrementPersediaanMasukId, 6, '0', STR_PAD_LEFT);

        // Bangun ID persediaan masuk baru dengan menambahkan awalan "PSM"
        $newPersediaanMasukId = "PSM." . $now->year . "." . $incrementPersediaanMasukIdFormatted;
        return $newPersediaanMasukId;
    }

}
