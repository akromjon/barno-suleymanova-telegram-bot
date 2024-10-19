<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\TelegramUserResource;
use App\Models\Enums\TransactionStatusEnum;
use App\Models\TelegramUser;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Transaction;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;

class TelegramUserTableWidget extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';
    protected static ?string $heading = 'Recently Active Users';

    protected static ?string $pollingInterval = '15s';
    public function table(Table $table): Table
    {
        return $table
            ->query(
                TelegramUser::query()
            )
            ->actions(TelegramUserResource::getActions())
            ->columns(TelegramUserResource::getTableColumns())
            ->defaultSort('last_used_at', 'desc');
    }
}
