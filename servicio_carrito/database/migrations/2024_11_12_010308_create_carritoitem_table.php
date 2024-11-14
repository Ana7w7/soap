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
        Schema::create('carritoitem', function (Blueprint $table) {
            $table->id();
            $table->foreignId('carrito_id')->references('id')->on('carrito')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('producto_id');
            $table->integer('cantidad')->nullable();
            $table->decimal('precio');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carritoitem');
    }
};
