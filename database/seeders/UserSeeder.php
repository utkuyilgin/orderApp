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
        User::updateOrCreate([
            'id' => 1,
            'name' => 'Utku Yılgın',
            'email' => 'utku.387@hotmail.com',
            'password' => \Hash::make('123456'),
        ], [
            'id' => 1,
            'name' => 'Utku Yılgın',
            'email' => 'utku.387@hotmail.com',
            'password' => \Hash::make('123456'),

        ]);
    }
}
