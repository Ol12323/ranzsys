<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class CreateRoles extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Owner',
                'description' => 'Manage sales and services',
            ],

            [
                'name' => 'Staff',
                'description' => 'Manage sales',
            ],

            [
                'name' => 'Customer',
                'description' => 'Purchase services',
            ],
        ];

        foreach($roles as $newRole){
            Role::create($newRole);
        }
    }
}
