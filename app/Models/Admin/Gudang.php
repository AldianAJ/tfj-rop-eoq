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
        $addZero = '';
        $gudang_id = str_replace("G", "", $gudang_id);
        $gudang_id = (int) $gudang_id + 1;
        $incrementGudangId = $gudang_id;

        if (strlen($gudang_id) == 1) {
            $addZero = "0000";
        } elseif (strlen($gudang_id) == 2) {
            $addZero = "000";
        } elseif (strlen($gudang_id) == 3) {
            $addZero = "00";
        } elseif (strlen($gudang_id) == 4) {
            $addZero = "0";
        }

        $newGudangId = "G" . $addZero . $incrementGudangId;
        return $newGudangId;
    }
}
