<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TelegramNotificationResource\Pages;
use App\Filament\Resources\TelegramNotificationResource\RelationManagers;
use App\Models\Enums\TelegramNotificationType;
use App\Models\TelegramNotification;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;


class TelegramNotificationResource extends Resource
{
    protected static ?string $model = TelegramNotification::class;

    protected static ?string $navigationLabel = "Notifications";

    protected static ?string $navigationGroup = "Telegram";
    public static function form(Form $form): Form
    {
        return $form
            ->schema(components: self::getForm());
    }

    private static function getForm(): array
    {
        return [
            TextInput::make(name: 'name')->maxLength(length: 255)->required(),

            Select::make(name: 'type')
                ->options(options: TelegramNotificationType::class)
                ->required()
                ->reactive()
                ->default(state: TelegramNotificationType::TEXT),

            FileUpload::make(name: 'file')
                ->columnSpan(span: 'full')
                ->helperText(text: 'Max: 20MB files are allowed')
                ->visible(condition: fn($get): bool => match ($get('type')) {
                    TelegramNotificationType::PHOTO,
                    TelegramNotificationType::VIDEO,
                    TelegramNotificationType::DOCUMENT,
                    TelegramNotificationType::AUDIO,
                    TelegramNotificationType::VOICE => true,
                    default => false,
                }),
            // add telegram Text field that supports html and markdown here
            Textarea::make(name: 'message')->required()->maxLength(length: 4096)->label(label: 'Message')
                ->required()
                ->columnSpan(span: 'full')
                ->rows(10)
                ->helperText(text: 'Supports basic HTML formatting (e.g., <b>, <i>, <a>).'),
            Toggle::make('is_active')->default(true)
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(components: [
                //
            ])
            ->filters(filters: [
                //
            ])
            ->actions(actions: [
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions(actions: [
                Tables\Actions\BulkActionGroup::make(actions: [
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTelegramNotifications::route('/'),
            'create' => Pages\CreateTelegramNotification::route('/create'),
            'edit' => Pages\EditTelegramNotification::route('/{record}/edit'),
        ];
    }
}
