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
        'gudang_id', 'slug', 'user_id'
    ];

    public static function generateGudangId()
    {
        $gudang_id = DB::table('gudangs')->max('gudang_id');

        $gudang_id = str_replace("G", "", $gudang_id);
        $gudang_id = (int) $gudang_id + 1;

        $addZero = str_pad($gudang_id, 5, "0", STR_PAD_LEFT);

        $newGudangId = "G{$addZero}";

        return $newGudangId;
    }
}
