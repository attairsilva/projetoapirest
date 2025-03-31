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
        Schema::create('unidade_endereco', function (Blueprint $table) {
            $table->unsignedBigInteger('unid_id');
            $table->unsignedBigInteger('end_id');

            // chaves estrangeiras
            $table->foreign('unid_id')->references('unid_id')->on('unidade')->onDelete('cascade');
            $table->foreign('end_id')->references('end_id')->on('endereco')->onDelete('cascade');

            // chave primária composta
            $table->primary(['unid_id', 'end_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unidade_endereco');
    }
};
