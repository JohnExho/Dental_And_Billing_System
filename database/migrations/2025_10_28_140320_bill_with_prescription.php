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
        Schema::table('bill_items', function (Blueprint $table) {
            $table->uuid('prescription_id')->nullable(); // Prescription must have the status "purchased" if "prescribed" don't bill
            $table->foreign('prescription_id')->references('prescription_id')->on('prescriptions')->onDelete('set null');
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
