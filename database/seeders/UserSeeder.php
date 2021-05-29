<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
        	'name' 		=> 'Jorge Otalvaro',
        	'email'		=> 'george15932@gmail.com',
        	'password' 	=> bcrypt('12345678')
        ]);

        User::factory(199)->create();
    }
}
