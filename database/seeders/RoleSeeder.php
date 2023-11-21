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
            ['name' => 'Super Administrateur',  'slug' => 'super_admin',    'parent_id' => 0 ],
            ['name' => 'Administrateur',        'slug' => 'admin'      ,    'parent_id' => 1 ],
            ['name' => 'Chef de service',       'slug' => 'chef_service',   'parent_id' => 2 ],
            ['name' => 'Medecin',               'slug' => 'medecin',        'parent_id' => 3 ],
            ['name' => 'Interne',               'slug' => 'intern',         'parent_id' => 4 ],
        ];

        collect($roles)->each(function ($role) {
            Role::create($role);
        });
    }
}