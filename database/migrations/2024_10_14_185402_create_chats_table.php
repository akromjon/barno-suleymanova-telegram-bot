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
        Schema::create(table: 'chats', callback: function (Blueprint $table): void {
            $table->id();
            $table->bigInteger(column: 'telegram_user_id');
            $table->enum(column: 'type',allowed: ['private', 'group', 'supergroup', 'channel'])->default('private');
            $table->foreign(columns: 'telegram_user_id')->references(columns: 'id')->on(table: 'telegram_users')->onDelete(action: 'cascade');
            $table->enum(column: 'status', allowed: ['active', 'closed'])->default(value: 'active');
            $table->timestamp(column: 'last_messaged_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(table: 'chats');
    }
};
