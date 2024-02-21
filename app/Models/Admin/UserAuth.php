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
        'user_id',
        'slug',
        'username',
        'name',
        'address',
        'password',
        'role',
        'status'
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
        // Ambil ID pengguna terakhir
        $user_id = DB::table('users')->max('user_id');

        // Jika tidak ada pengguna sebelumnya, atur nomor urut menjadi 1
        $incrementUserId = !empty($user_id) ? ((int) substr($user_id, 1) + 1) : 1;

        // Format nomor urut dengan tambahan nol di depan sesuai panjang
        $incrementUserIdFormatted = str_pad($incrementUserId, 5, '0', STR_PAD_LEFT);

        // Bangun ID pengguna baru dengan menambahkan awalan "U"
        $newUserId = "U" . $incrementUserIdFormatted;
        return $newUserId;
    }

}
