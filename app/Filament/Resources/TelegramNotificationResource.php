<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TelegramNotificationResource\Pages;
use App\Filament\Resources\TelegramNotificationResource\RelationManagers;
use App\Jobs\CopyMessageGroupJob;
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
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Support\Collection;

class TelegramNotificationResource extends Resource
{
    protected static ?string $model = TelegramNotification::class;

    protected static ?string $navigationLabel = "Notifications";

    protected static ?string $navigationGroup = "Telegram";

    public static function getNavigationBadge(): ?string
    {
        return TelegramNotification::count();
    }
    public static function form(Form $form): Form
    {
        return $form->schema(components: self::getForm());
    }

    private static function getForm(): array
    {
        return [
            TextInput::make(name: 'name')->columnSpan(span: 'full')->maxLength(length: 255)->required(),

            TextInput::make(name: 'message_id')->label('Message ID')->disabled(),

            Select::make(name: 'type')
                ->options(options: TelegramNotificationType::class)
                ->required()
                ->reactive()
                ->default(state: TelegramNotificationType::TEXT),

            FileUpload::make(name: 'file')
                ->columnSpan(span: 'full')
                ->helperText(text: 'Max: 20MB files are allowed'),
            // add telegram Text field that supports html and markdown here
            Textarea::make(name: 'text')->visible(condition: function (Get $get): bool {

                return match ($get('type')) {
                    TelegramNotificationType::VOICE => false,
                    default => true

                };

            })->required()->maxLength(length: function (Get $get): int {
                return match ($get('type')) {
                    TelegramNotificationType::TEXT => 4096,
                    default => 1024,
                };
            })->label(label: 'Text')
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
            ->actions(actions: self::getActions())
            ->defaultSort('id', 'desc')
            ->bulkActions(actions: self::getBulkActions());
    }

    public static function getActions(): array
    {
        return [
            Tables\Actions\ViewAction::make(),
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ];
    }

    public static function getBulkActions(): array
    {
        return [
            Tables\Actions\BulkActionGroup::make(actions: [
                Tables\Actions\DeleteBulkAction::make(),
                BulkAction::make('sendNotifications')
                    ->label('🔔 Send Notifications')
                    ->requiresConfirmation()
                    ->action(function (Collection $records) {

                        // Check if more than 3 records are selected
                        if ($records->count() > 3) {
                            // Show a message if more than 3 records are selected
                            Notification::make()
                                ->title('You can send a maximum of 3 notifications.')
                                ->danger() // Use danger for warnings
                                ->send();

                        } else {
                            // Process the notification sending for 3 or fewer records
                            Notification::make()
                                ->title('Notifications started sending')
                                ->success()
                                ->send();

                            CopyMessageGroupJob::dispatch($records);

                            // Add the logic to send the actual notifications here if needed
                        }
                    }),

            ]),
        ];
    }

    public static function getColumns(): array
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
                    $text = is_array(value: $state) ? implode(separator: ', ', array: $state) : $state;
                    return "{$text} users";
                })
                ->searchable()
                ->sortable(),
            ToggleColumn::make(name: 'is_active')->sortable()->searchable(),
            TextColumn::make(name: 'created_at')->dateTime()->sortable()->searchable(),
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
