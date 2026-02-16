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
        Schema::create('sale_ticket', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignId('sale_id') 
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');   


            $table->foreignId('ticket_id') 
                ->constrained()
                ->onUpdate('cascade') //Cette fois j'ai choisi cascade pour les clés étrangères. Donc si on supprime une catégorie ou un film la table pivot sera automatiquement mise à jour.
                ->onDelete('cascade');   
            //$table->timestamps();
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
