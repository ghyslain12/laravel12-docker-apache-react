<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('material_sale', function (Blueprint $table) {
            //$table->id()->nullable();
            $table->timestamps();

            $table->foreignId('sale_id')
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade')->nullable();


            $table->foreignId('material_id')
                ->constrained()
                ->onUpdate('cascade') //Cette fois j'ai choisi cascade pour les clés étrangères. Donc si on supprime une catégorie ou un film la table pivot sera automatiquement mise à jour.
                ->onDelete('cascade')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
