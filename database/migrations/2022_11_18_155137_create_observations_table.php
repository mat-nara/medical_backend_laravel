<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateObservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('observations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            #$table->string('patient_id', 20);
            $table->string('historique')->nullable();
            
            $table->json('antecedent_toxique')->nullable();
            $table->json('antecedent_allergique')->nullable();
            $table->string('exam_phys_signe_gen')->nullable();
            $table->json('exam_phys_signe_gen_score_indice')->nullable();
            $table->string('exam_phys_signe_fonc')->nullable();
            $table->json('exam_phys_signe_fonc_score_indice')->nullable();
            $table->string('exam_phys_signe_phys_cardio')->nullable();
            $table->json('exam_phys_signe_phys_cardio_score_indice')->nullable();
            $table->string('exam_phys_signe_phys_pleuro')->nullable();
            $table->json('exam_phys_signe_phys_pleuro_score_indice')->nullable();
            $table->string('exam_phys_signe_phys_neuro')->nullable();
            $table->json('exam_phys_signe_phys_neuro_score_indice')->nullable();
            $table->string('exam_phys_signe_phys_abdo')->nullable();
            $table->json('exam_phys_signe_phys_abdo_score_indice')->nullable();
            $table->string('conclusion')->nullable();
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
        Schema::dropIfExists('observations');
    }
}
