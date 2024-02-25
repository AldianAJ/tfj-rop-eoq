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

        $counter_id = str_replace("C", "", $counter_id);
        $counter_id = (int) $counter_id + 1;

        $addZero = str_pad($counter_id, 5, "0", STR_PAD_LEFT);

        $newCounterId = "C{$addZero}";

        return $newCounterId;
    }
}
