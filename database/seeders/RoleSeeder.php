<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'Administrator', 'slug' => 'admin'],
            ['name' => 'Host',          'slug' => 'host'],
            ['name' => 'Tenant',        'slug' => 'tenant'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['slug' => $role['slug']], $role);
        }
    }
}
