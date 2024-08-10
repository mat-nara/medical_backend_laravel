<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTraitementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('traitements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('code')->nullable();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->string('DCI')->nullable();
            $table->string('forme')->nullable();
            $table->string('posologie')->nullable();
            $table->json('prise_journalier')->nullable();
            $table->enum('etat', ['actif', 'termine', 'arrete', 'modifie'])->default('actif');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            //$table->json('value')->nullable();
            //$table->json('suivi_prise')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('traitements');
    }
}
