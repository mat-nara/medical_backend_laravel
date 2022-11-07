<?php

namespace Database\Seeders;

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

        $user = User::create([
            'name' => 'RAKOTOARISOA Mahefa Théodule',
            'email' => 'mahefatheodule@gmail.com',
            'password' => Hash::make('123')
        ]);
        $user->roles()->attach(Role::where('slug', 'admin')->first());
    }
}