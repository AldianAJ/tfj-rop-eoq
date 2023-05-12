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
        'counter_id', 'slug', 'user_id'
    ];

    public static function generateCounterId()
    {
        $counter_id = DB::table('counters')->max('counter_id');
        $addZero = '';
        $counter_id = str_replace("C", "", $counter_id);
        $counter_id = (int) $counter_id + 1;
        $incrementCounterId = $counter_id;

        if (strlen($counter_id) == 1) {
            $addZero = "0000";
        } elseif (strlen($counter_id) == 2) {
            $addZero = "000";
        } elseif (strlen($counter_id) == 3) {
            $addZero = "00";
        } elseif (strlen($counter_id) == 4) {
            $addZero = "0";
        }

        $newCounterId = "C" . $addZero . $incrementCounterId;
        return $newCounterId;
    }
}
