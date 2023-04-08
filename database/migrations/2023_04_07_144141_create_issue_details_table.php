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
        Schema::create('issue_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('requisition_id');
            $table->foreign('requisition_id')->references('id')->on('requisitions')->onDelete('cascade');
            $table->unsignedBigInteger('issue_id');
            $table->foreign('issue_id')->references('id')->on('issues')->onDelete('cascade');
            $table->unsignedBigInteger('item_id');
            $table->foreign('item_id')->references('id')->on('items');
            $table->unsignedInteger('quantity');
            $table->decimal('price', 8, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issue_details');
    }
};
