<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\TelegramNotificationResource;
use App\Filament\Resources\TelegramUserResource;
use App\Models\Enums\TransactionStatusEnum;
use App\Models\TelegramNotification;
use App\Models\TelegramUser;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Transaction;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;

class TelegramNotificationTableWidget extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';
    protected static ?string $heading = 'Notifications';
    protected static ?string $pollingInterval = '15s';
    public function table(Table $table): Table
    {
        return $table
            ->query(
                TelegramNotification::query()
            )
            ->actions(TelegramNotificationResource::getActions())
            ->bulkActions(TelegramNotificationResource::getBulkActions())
            ->columns(TelegramNotificationResource::getColumns())
            ->defaultSort('created_at', 'desc');
    }
}
