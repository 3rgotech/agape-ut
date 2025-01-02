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
        Schema::dropIfExists('application_laboratory');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('application_laboratory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('applications')->cascadeOnDelete();
            $table->foreignId('laboratory_id')->constrained('laboratories')->cascadeOnDelete();
            $table->string('contact_name')->nullable();
            $table->unsignedTinyInteger('order');
            $table->unique(['application_id', 'laboratory_id']);
        });
    }
};
