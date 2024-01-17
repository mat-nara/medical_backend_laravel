<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuivieTraitementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suivie_traitements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('traitement_id');
            $table->unsignedBigInteger('user_id');
            $table->date('date')->nullable();
            $table->time('heure')->nullable();
            $table->time('heure_finale')->nullable();
            $table->string('commentaire')->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('suivie_traitements');
    }
}
