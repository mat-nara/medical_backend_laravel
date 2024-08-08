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
            $table->unsignedBigInteger('service_id');
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
            $table->date('date_entree')->nullable();
            $table->date('date_sortie')->nullable();
            $table->time('heure_entree')->nullable();
            $table->time('heure_sortie')->nullable();
            $table->string('motif_entree')->nullable();
            $table->enum('etat', ['admis', 'ferme', 'transfere', 'decede'])->default('admis');
            $table->string('commentaire')->nullable();
            

            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));

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
