<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Counter extends Model
{
    use HasFactory;

    protected $table = 'counters';
    protected $primaryKey = 'counter_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'counter_id',
        'slug',
        'user_id'
    ];

    public static function generateCounterId()
    {
        // Ambil ID counter terakhir
        $lastCounterId = DB::table('counters')->max('counter_id');

        // Jika tidak ada data counter sebelumnya, atur nomor urut menjadi 1
        $incrementCounterId = !empty($lastCounterId) ? ((int) substr($lastCounterId, 1) + 1) : 1;

        // Format nomor urut dengan tambahan nol di depan sesuai panjang
        $incrementCounterIdFormatted = str_pad($incrementCounterId, 5, '0', STR_PAD_LEFT);

        // Bangun ID counter baru dengan menambahkan awalan "C"
        $newCounterId = "C" . $incrementCounterIdFormatted;
        return $newCounterId;
    }

}
