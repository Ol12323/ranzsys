<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;

class CreateUsers extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = Role::pluck('id')->toArray();

        User::create([
            'last_name' => 'Dalumpines',
            'first_name' => 'Stanley',
            'avatar' => 'default.png',
            'phone_number' => '09514093271',
            'date_of_birth' => '2001-11-02',
            'address' => 'Homelabd Subd., Dapco, Panabo city',
            'role_id' => $roles[1],
            'email' => 'stanley.dalumpines@gmail.com',
            'password' => bcrypt('password'),
            'is_banned' => false,
        ]);
    }
}
