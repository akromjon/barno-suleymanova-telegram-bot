<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(table: 'telegram_users', callback: function (Blueprint $table): void {
            $table->id();
            $table->bigInteger(column: 'chat_id');
            $table->string(column: "first_name");
            $table->string(column: 'last_name')->nullable();
            $table->string(column: 'username')->nullable();
            $table->string(column: "phone_number")->nullable();
            $table->timestamp(column: 'last_used_at')->nullable();
            $table->enum(column: 'chat_status', allowed: ['active', 'blocked'])->default(value: 'active');
            $table->enum(column: 'status', allowed: ['active', 'inactive'])->default(value: 'active');
            $table->boolean(column: 'subscribed')->default(value: false);
            $table->index(columns: 'chat_id');
            $table->string(column: 'tags')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('telegram_users');
    }
};
