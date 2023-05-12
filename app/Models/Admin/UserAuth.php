<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;

class UserAuth extends Authenticatable
{
    use HasFactory;

    protected $table = 'users';

    protected $primaryKey = 'user_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id', 'slug', 'username', 'name', 'address', 'password', 'role', 'status'
    ];

    protected $hidden = [
        'password'
    ];

    public function getAuthPassword()
    {
        return $this->password;
    }

    public static function generateUserId()
    {
        $user_id = DB::table('users')->max('user_id');
        $addZero = '';
        $user_id = str_replace("U", "", $user_id);
        $user_id = (int) $user_id + 1;
        $incrementUserId = $user_id;

        if (strlen($user_id) == 1) {
            $addZero = "0000";
        } elseif (strlen($user_id) == 2) {
            $addZero = "000";
        } elseif (strlen($user_id) == 3) {
            $addZero = "00";
        } elseif (strlen($user_id) == 4) {
            $addZero = "0";
        }

        $newUserId = "U" . $addZero . $incrementUserId;
        return $newUserId;
    }
}
