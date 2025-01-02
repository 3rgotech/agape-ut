<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('carriers', function (Blueprint $table) {
            $table->foreignId('application_id')->after('id')->constrained('applications')->cascadeOnDelete();
            $table->foreignId('laboratory_id')->after('application_id')->constrained('laboratories')->cascadeOnDelete();
            $table->boolean('main_carrier')->after('laboratory_id');
            $table->string('job_title')->nullable()->after('phone');
            $table->string('job_title_other')->nullable()->after('job_title');
            $table->string('organization')->nullable()->after('job_title_other');
            $table->string('organization_type')->nullable()->after('organization');
            $table->string('organization_type_other')->nullable()->after('organization_type');
            $table->dropColumn('status');
        });

        Artisan::call('agape:migrate-carriers');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carriers');
        Schema::create('carriers', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone');
            $table->string('status');
            $table->timestamps();
        });
    }
};
