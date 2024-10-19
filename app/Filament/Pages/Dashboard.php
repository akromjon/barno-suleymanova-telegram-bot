<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\TelegramUserTableWidget;
use App\Filament\Widgets\QuizOverview;
use App\Filament\Widgets\TelegramNotificationTableWidget;
use App\Filament\Widgets\TelegramUserStats;
use App\Filament\Widgets\TelegramUserStatsOverview;
use App\Filament\Widgets\TelegramUserTrendWidget;
use App\Filament\Widgets\TransactionTableWidget;
use App\Filament\Widgets\TransactionWidget;
use Filament\Pages\Dashboard as PagesDashboard;

class Dashboard extends PagesDashboard
{

    public function getWidgets(): array
    {
        return [
            TelegramUserStats::class,
            TelegramUserTrendWidget::class,
            TelegramUserTableWidget::class,
            TelegramNotificationTableWidget::class
        ];
    }

}
