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
        // 3. then in a separate migration, after both exist:
        Schema::table('accounts', function (Blueprint $table) {
              $table->foreign('clinic_id')->references('clinic_id')->on('clinics')->onDelete('set null');
            $table->foreign('laboratory_id')->references('laboratory_id')->on('laboratories')->onDelete('set null');
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
