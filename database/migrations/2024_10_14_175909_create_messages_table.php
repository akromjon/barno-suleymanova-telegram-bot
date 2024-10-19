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
        Schema::create(table: 'messages', callback: function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger(column: 'chat_id');
            $table->unsignedBigInteger(column: 'user_id')->nullable();
            $table->bigInteger(column: 'message_id');
            $table->bigInteger(column: 'telegram_user_id');

            $table->enum(column: 'sender', allowed: ['bot', 'telegram_user']);


            $table->enum(column: 'type', allowed: [
                'text',
                'photo',
                'audio',
                'voice',
                'video',
                'animation',
                'document',
                'sticker',
                'location',
                'venue',
                'contact',
                'poll',
                'invoice',
                'dice',
                'media_group',
                'game',
                'callback_query',
                'edited_message',
                'channel_post',
                'unsupport',
                'video_note',
            ]);

            $table->boolean(column: 'is_read')->default(value: false);

            $table->string(column: 'text', length: 4096)->nullable();
            $table->string(column: 'file')->nullable();
            $table->string(column: 'thumb')->nullable();

            $table->foreign(columns: 'chat_id')->references(columns: 'id')->on(table: 'chats')->onDelete(action: 'cascade');
            $table->foreign(columns: 'user_id')->references(columns: 'id')->on(table: 'users')->onDelete(action: 'cascade');
            $table->foreign(columns: 'telegram_user_id')->references(columns: 'id')->on(table: 'telegram_users')->onDelete(action: 'cascade');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(table: 'messages');
    }
};
