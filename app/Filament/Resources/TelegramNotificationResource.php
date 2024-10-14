<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TelegramNotificationResource\Pages;
use App\Filament\Resources\TelegramNotificationResource\RelationManagers;
use App\Models\Enums\TelegramNotificationFormatType;
use App\Models\Enums\TelegramNotificationSendTo;
use App\Models\Enums\TelegramNotificationType;
use App\Models\TelegramNotification;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
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
                ->helperText(text: 'Max: 20MB files are allowed'),
            // add telegram Text field that supports html and markdown here
            Textarea::make(name: 'message')->visible(condition: function (Get $get): bool {

                return match ($get('type')) {
                    TelegramNotificationType::VOICE => false,
                    default => true

                };

            })->required()->maxLength(length: function (Get $get): string {
                return match ($get('type')) {
                    TelegramNotificationType::TEXT => 4096,
                    default => 1024,
                };
            })->label(label: 'Message')
                ->required()
                ->columnSpan(span: 'full')
                ->rows(10)
                ->helperText(function (Get $get): string {
                    $limit = match ($get('type')) {
                        TelegramNotificationType::TEXT => 4096,
                        default => 1024,

                    };
                    return 'Supports basic HTML formatting (e.g., <b>, <i>, <a>). Limit: ' . $limit;
                }),
            Select::make(name: 'send_to')->columnSpanFull()->options(TelegramNotificationSendTo::class)->required()->multiple(),
            ToggleButtons::make(name: 'format_type')
                ->options(options: TelegramNotificationFormatType::class),
            Toggle::make(name: 'is_active')->default(true)
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(components: self::getColumns())
            ->filters(filters: [
                //
            ])
            ->actions(actions: [
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),

            ])
            ->bulkActions(actions: [
                Tables\Actions\BulkActionGroup::make(actions: [
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    private static function getColumns(): array
    {
        return [
            TextColumn::make(name: 'name')->sortable()->searchable(),
            TextColumn::make(name: 'file')->label(label: 'File')->formatStateUsing(callback: function ($record): string {
                return "📄 View a file";
            })->url(function ($record): string {
                return "/storage/" . $record->file;
            })->openUrlInNewTab(condition: true),
            TextColumn::make(name: 'send_to')
                ->formatStateUsing(callback: function ($state): mixed {
                    return is_array(value: $state) ? implode(separator: ', ', array: $state) : $state;
                })
                ->searchable()
                ->sortable(),
            ToggleColumn::make(name: 'is_active')->sortable()->searchable()
        ];
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
