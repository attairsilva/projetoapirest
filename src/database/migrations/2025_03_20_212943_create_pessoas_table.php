<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public $timestamps = false;
    public function up(): void
    {
        Schema::create('pessoa', function (Blueprint $table) {
            $table->id('pes_id'); // ID primário
            $table->string('pes_nome', 255);
            $table->date('pes_data_nascimento')->nullable();
            $table->enum('pes_sexo', ['M', 'F', 'O'])->nullable(); // Masculino, Feminino ou Outro
            $table->string('pes_mae', 255)->nullable();
            $table->string('pes_pai', 255)->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pessoa');
    }
};
