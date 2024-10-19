<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TelegramUserResource\Pages;
use App\Models\Enums\TelegramUserChatStatus;
use App\Models\Enums\TelegramUserRole;
use App\Models\Enums\TelegramUserStatus;
use App\Models\Enums\TelegramUserType;
use App\Models\TelegramUser;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class TelegramUserResource extends Resource
{
    protected static ?string $model = TelegramUser::class;

    protected static ?string $navigationLabel = "Users";

    protected static ?string $navigationGroup = "Telegram";

    public static function getNavigationBadge(): ?string
    {
        return TelegramUser::where(column: 'chat_status', operator: TelegramUserChatStatus::ACTIVE)->count() . "/" . TelegramUser::count();
    }

    public static function form(Form $form): Form
    {
        return $form->schema(components: self::getForm());
    }

    private static function getForm(): array
    {
        return [
            TextInput::make(name: 'chat_id')->disabled(),
            TextInput::make(name: 'first_name')->required(),
            TextInput::make(name: 'last_name'),
            TextInput::make(name: 'username'),
            TextInput::make(name: 'phone_number'),
            DateTimePicker::make(name: 'last_used_at')->default(state: now()),
            Select::make(name: 'type')->options(options: TelegramUserType::class),
            Select::make(name: 'chat_status')->options(options: TelegramUserChatStatus::class),
            Select::make(name: 'role')->options(options: TelegramUserRole::class),
            Select::make(name: 'status')->options(options: TelegramUserStatus::class),
            Toggle::make(name: 'subscribed')->default(state: false),
            Textarea::make(name: 'tags')->maxLength(length: 255)
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(components: self::getTableColumns())
            ->filters(filters: self::getFilters())
            ->actions(actions: self::getActions())
            ->defaultSort(column: 'last_used_at', direction: 'desc')
            ->bulkActions(actions: self::getBulkActions());
    }

    public static function getActions(): array
    {
        return [
            Tables\Actions\ViewAction::make(),
            Tables\Actions\EditAction::make(),
        ];
    }

    public static function getBulkActions(): array
    {
        return [
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
            ])
        ];
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTelegramUsers::route(path: '/'),
            'edit' => Pages\EditTelegramUser::route(path: '/{record}/edit'),
        ];
    }

    public static function getTableColumns(): array
    {
        return [
            TextColumn::make(name: 'id')->toggleable(isToggledHiddenByDefault: true)->sortable()->searchable(),
            TextColumn::make(name: 'chat_id')->label(label: 'Chat ID')->sortable()->copyable()->searchable(),
            TextColumn::make(name: 'first_name')->formatStateUsing(callback: function ($record): string {
                return Str::limit(value: $record->first_name, limit: 10);
            })->sortable()->searchable(),
            TextColumn::make(name: 'last_name')->toggleable(isToggledHiddenByDefault: true)->formatStateUsing(callback: function ($record): string {
                return Str::limit(value: $record->last_name, limit: 10);
            })->sortable()->searchable(),
            TextColumn::make(name: 'username')->searchable()->copyable()->sortable(),
            TextColumn::make(name: 'chat')->label(label: 'CHAT')->formatStateUsing(callback: function ($record): string {
                return "💬 CHAT";
            })->url(function ($record): string {
                return route('chats.show', $record->id);
            })->openUrlInNewTab(condition: true),
            TextColumn::make(name: 'type')->searchable()->copyable()->sortable(),
            TextColumn::make(name: 'role')
                ->badge(),
            TextColumn::make(name: 'last_used_at')->label(label: 'Last Used At')->dateTime()->sortable()->searchable(),
            SelectColumn::make(name: 'status')->options(options: TelegramUserStatus::class)->searchable()->sortable(),
            TextColumn::make(name: 'chat_status')
                ->badge()
                ->color(color: fn(TelegramUserChatStatus $state): string => match ($state) {
                    TelegramUserChatStatus::ACTIVE => 'success',
                    TelegramUserChatStatus::BLOCKED => 'danger',
                }),
            ToggleColumn::make(name: 'subscribed')->searchable()->sortable(),
            TextColumn::make(name: 'tags')->toggleable(isToggledHiddenByDefault: true)->searchable()->copyable()->sortable(),
        ];
    }

    private static function getFilters(): array
    {
        return [
            Filter::make(name: 'subscribed')
                ->query(callback: fn(Builder $query): Builder => $query->where(column: 'subscribed', operator: true))
                ->toggle()
                ->label(label: 'Subscribed'),
            SelectFilter::make(name: 'chat_status')
                ->options(options: TelegramUserChatStatus::class),
            SelectFilter::make(name: 'status')
                ->options(options: TelegramUserStatus::class),

        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
