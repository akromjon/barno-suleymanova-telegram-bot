<?php

namespace App\Filament\Widgets;

use App\Models\Enums\TelegramUserChatStatus;
use App\Models\TelegramUser;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;
class TelegramUserStats extends BaseWidget
{
    protected static ?string $pollingInterval = '5s';
    protected function getStats(): array
    {
        return [
            Stat::make('👤 Total Users', TelegramUser::count())
                ->color('success'),

            Stat::make('⚡ Total Active Users', TelegramUser::where('chat_status', TelegramUserChatStatus::ACTIVE)->count())
                ->color('success'),

            Stat::make('❌ Blocked Users', TelegramUser::where('chat_status', TelegramUserChatStatus::BLOCKED)->count())
                ->color('success'),

            Stat::make('💎 Total Subscribed Users', TelegramUser::where('chat_status', TelegramUserChatStatus::ACTIVE)
                ->where('subscribed', true)->count())
                ->color('success'),

            // Adding weekly active users
            Stat::make('📅 Weekly Active Users', TelegramUser::where('last_used_at', '>=', Carbon::now()->subWeek())
                ->where('chat_status', TelegramUserChatStatus::ACTIVE)->count())
                ->color('info'),

            // Adding daily active users
            Stat::make('📅 Daily Active Users', TelegramUser::where('last_used_at', '>=', Carbon::now()->subDay())
                ->where('chat_status', TelegramUserChatStatus::ACTIVE)->count())
                ->color('info'),


        ];
    }
}
