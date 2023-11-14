<?php

namespace Database\Seeders;

use App\Models\Hopital;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class HopitalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('hopitals')->truncate();
        Schema::enableForeignKeyConstraints();

        $hopital = Hopital::create([
            'nom'       => 'Epionia',
            'adresse'   => '',
            'ville'     => ''
        ]);
    }
}
