<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin\UserAuth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $datas = file_get_contents('database/seeders/json/User.json');
        $datas = json_decode($datas);
        foreach ($datas as $data) {
            DB::beginTransaction();
            try {
                $user_id = UserAuth::generateUserId();
                $user = new UserAuth;
                $user->user_id = $user_id;
                $user->slug = Str::random(16);
                $user->name = $data->name;
                $user->address = $data->address;
                $user->username = $data->username;
                $user->password = bcrypt($data->password);
                $user->role = $data->role;
                $user->status = $data->status;
                $user->save();
                DB::commit();
            } catch (\Exception $ex) {
                echo $ex->getMessage();
                DB::rollBack();
            }
        }
    }
}
