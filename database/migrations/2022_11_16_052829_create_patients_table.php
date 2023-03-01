<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            #$table->string('id', 20)->primary();
            $table->string('n_dossier')->nullable();
            $table->string('n_bulletin')->nullable();
            $table->string('nom')->nullable();
            $table->string('prenoms')->nullable();
            $table->string('genre')->nullable();
            $table->string('dob')->nullable();
            $table->string('age')->nullable();
            $table->string('lieu_dob')->nullable();
            $table->string('status')->nullable();
            $table->string('profession')->nullable();
            $table->string('adresse')->nullable();
            $table->string('ville')->nullable();
            $table->string('telephone')->nullable();
            $table->string('personne_en_charge')->nullable();
            $table->string('contact_pers_en_charge')->nullable();
            $table->string('date_entree')->nullable();
            $table->string('motif_entree')->nullable();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('patients');
    }
}
