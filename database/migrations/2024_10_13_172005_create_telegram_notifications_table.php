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
        Schema::create(table: 'telegram_notifications', callback: function (Blueprint $table): void {
            $table->id();
            $table->string(column: 'name');
            $table->string(column: 'text', length: 4096)->nullable();
            $table->string(column: 'file')->nullable();
            $table->json(column: 'inline_buttons')->nullable();
            $table->bigInteger(column: 'from_chat_id')->nullable();
            $table->bigInteger(column: 'message_id')->nullable();
            $table->enum(column: 'type', allowed: [
                'text',
                'photo',
                'audio',
                'voice',
                'video',
                'video_note',
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
                'copy_message',
            ])->default(value: 'text');
            $table->enum(column: 'format_type', allowed: ['HTML', 'Markdown'])->default('HTML');
            $table->boolean(column: 'is_active')->default(value: true);
            $table->json(column: 'send_to')->default(value: 'all');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(table: 'telegram_notifications');
    }
};
