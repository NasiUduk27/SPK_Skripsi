<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nilai_alternatifs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alternatif_id')->constrained('alternatifs')->onDelete('cascade');
            $table->foreignId('kriteria_id')->constrained('kriterias')->onDelete('cascade');
            $table->double('nilai'); 
            $table->timestamps();

            $table->unique(['alternatif_id', 'kriteria_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nilai_alternatifs');
    }
};
