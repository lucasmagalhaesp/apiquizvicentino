<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\User;
use Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new User();
        $user->name = "Admin";
        $user->email = "admin@quizvicentino.com.br";
        $user->password = Hash::make("123456");
        $user->state = "MG";
        $user->city = "Ibirité";
        $user->admin = "S";

        $user->save();
    }
}
