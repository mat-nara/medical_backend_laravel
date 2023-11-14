<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('roles')->truncate();
        Schema::enableForeignKeyConstraints();

        $roles = [
            ['name' => 'Super Administrateur',  'slug' => 'super_admin'],
            ['name' => 'Administrateur',        'slug' => 'admin'],
            ['name' => 'Chef de service',       'slug' => 'chef_service'],
            ['name' => 'Medecin',               'slug' => 'medecin'],
            ['name' => 'Interne',               'slug' => 'intern'],
        ];

        collect($roles)->each(function ($role) {
            Role::create($role);
        });
    }
}