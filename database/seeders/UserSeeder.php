<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('users')->truncate();
        Schema::enableForeignKeyConstraints();

        $service = Service::where('name', 'Administration')->first();

        $user = User::create([
            'service_id'    => $service->id,
            'parent_id'     => 0,
            'name'          => 'RAKOTOARISOA Mahefa Théodule',
            'email'         => 'mahefatheodule@gmail.com',
            'password'      => Hash::make('123')
        ]);
        $user->roles()->attach(Role::where('slug', 'super_admin')->first());
    }
}