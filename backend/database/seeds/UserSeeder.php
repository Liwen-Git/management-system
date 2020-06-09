<?php

use App\Http\Modules\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::query()->where('name', 'admin')->first();
        if (!empty($user)) {
            $user->password = Hash::make('111111');
            $user->is_super = 1;
            $user->save();
        }else {
            $user = New User();
            $user->name = 'admin';
            $user->password = Hash::make('111111');
            $user->is_super = 1;
            $user->save();
        }
    }
}
