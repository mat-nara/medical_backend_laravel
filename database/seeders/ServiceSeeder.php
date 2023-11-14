<?php

namespace Database\Seeders;

use App\Models\Hopital;
use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('services')->truncate();

        $hopital = hopital::where('nom', 'Epionia')->first();

        $user = Service::create([
            'hopital_id'    => $hopital->id,
            'head_id'       => 1,
            'name'          => 'Administration',
        ]);
        Schema::enableForeignKeyConstraints();
    }
}
