<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('account_login_tokens', function (Blueprint $table) {
            $table->uuid('token_id')->primary(); // UUID as primary key
                     $table->uuid('account_id'); 
            $table->string('token', 100)->unique();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            
                        $table->foreign('account_id')
                  ->references('account_id')
                  ->on('accounts')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_login_tokens');
    }
};
