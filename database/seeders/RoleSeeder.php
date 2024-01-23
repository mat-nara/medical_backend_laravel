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
            ['id' => 1, 'hierarchic_level'=> 1,  'name' => 'Super Administrateur',  'slug' => 'super_admin',    'parent_id' => 0 ],
            ['id' => 2, 'hierarchic_level'=> 2,  'name' => 'Administrateur',        'slug' => 'admin'      ,    'parent_id' => 1 ],
            ['id' => 3, 'hierarchic_level'=> 3,  'name' => 'Chef de service',       'slug' => 'chef_service',   'parent_id' => 2 ],
            ['id' => 4, 'hierarchic_level'=> 4,  'name' => 'Medecin',               'slug' => 'medecin',        'parent_id' => 3 ],
            ['id' => 5, 'hierarchic_level'=> 5,  'name' => 'Interne',               'slug' => 'intern',         'parent_id' => 4 ],

            ['id' => 6, 'hierarchic_level'=> 3,  'name' => 'Major',                 'slug' => 'major',          'parent_id' => 2 ],
            ['id' => 7, 'hierarchic_level'=> 4,  'name' => 'Infirmier',             'slug' => 'infirmier',      'parent_id' => 6 ],
            ['id' => 8, 'hierarchic_level'=> 4,  'name' => 'Sage femme',            'slug' => 'sage_femme',     'parent_id' => 6 ],
        ];

        collect($roles)->each(function ($role) {
            Role::create($role);
        });
    }
}