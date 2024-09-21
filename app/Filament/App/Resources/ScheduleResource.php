<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\ScheduleResource\Pages;
use App\Filament\App\Resources\ScheduleResource\RelationManagers\TasksRelationManager;
use App\Models\Schedule;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;

class ScheduleResource extends Resource
{
    protected static ?string $model = Schedule::class;
    protected static ?int $navigationSort = 6;

    protected static ?string $navigationIcon = 'tabler-clock';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(10)
            ->schema([
                TextInput::make('name')
                    ->columnSpan(10)
                    ->label('Schedule Name')
                    ->placeholder('A human readable identifier for this schedule.')
                    ->autocomplete(false)
                    ->required(),
                Toggle::make('only_when_online')
                    ->label('Only when Server is Online?')
                    ->hintIconTooltip('Only execute this schedule when the server is in a running state.')
                    ->hintIcon('tabler-question-mark')
                    ->columnSpan(5)
                    ->required()
                    ->default(1),
                Toggle::make('is_active')
                    ->label('Enable Schedule?')
                    ->hintIconTooltip('This schedule will be executed automatically if enabled.')
                    ->hintIcon('tabler-question-mark')
                    ->columnSpan(5)
                    ->required()
                    ->default(1),
                TextInput::make('cron_minute')
                    ->columnSpan(2)
                    ->label('Minute')
                    ->default('*/5')
                    ->required(),
                TextInput::make('cron_hour')
                    ->columnSpan(2)
                    ->label('Hour')
                    ->default('*')
                    ->required(),
                TextInput::make('cron_day_of_month')
                    ->columnSpan(2)
                    ->label('Day of Month')
                    ->default('*')
                    ->required(),
                TextInput::make('cron_month')
                    ->columnSpan(2)
                    ->label('Month')
                    ->default('*')
                    ->required(),
                TextInput::make('cron_day_of_week')
                    ->columnSpan(2)
                    ->label('Day of Week')
                    ->default('*')
                    ->required(),
                Section::make('Presets')
                    ->schema([
                        Actions::make([
                            Action::make('hourly')
                                ->disabled(fn (string $operation) => $operation === 'view')
                                ->action(function (Set $set) {
                                    $set('cron_minute', '0');
                                    $set('cron_hour', '*');
                                    $set('cron_day_of_month', '*');
                                    $set('cron_month', '*');
                                    $set('cron_day_of_week', '*');
                                }),
                            Action::make('daily')
                                ->disabled(fn (string $operation) => $operation === 'view')
                                ->action(function (Set $set) {
                                    $set('cron_minute', '0');
                                    $set('cron_hour', '0');
                                    $set('cron_day_of_month', '*');
                                    $set('cron_month', '*');
                                    $set('cron_day_of_week', '*');
                                }),
                            Action::make('weekly')
                                ->disabled(fn (string $operation) => $operation === 'view')
                                ->action(function (Set $set) {
                                    $set('cron_minute', '0');
                                    $set('cron_hour', '0');
                                    $set('cron_day_of_month', '*');
                                    $set('cron_month', '*');
                                    $set('cron_day_of_week', '0');
                                }),
                            Action::make('monthly')
                                ->disabled(fn (string $operation) => $operation === 'view')
                                ->action(function (Set $set) {
                                    $set('cron_minute', '0');
                                    $set('cron_hour', '0');
                                    $set('cron_day_of_month', '1');
                                    $set('cron_month', '*');
                                    $set('cron_day_of_week', '0');
                                }),
                            Action::make('every_x_minutes')
                                ->disabled(fn (string $operation) => $operation === 'view')
                                ->form([
                                    TextInput::make('x')
                                        ->label('')
                                        ->numeric()
                                        ->minValue(1)
                                        ->maxValue(60)
                                        ->prefix('Every')
                                        ->suffix('Minutes'),
                                ])
                                ->action(function (Set $set, $data) {
                                    $set('cron_minute', '*/' . $data['x']);
                                    $set('cron_hour', '*');
                                    $set('cron_day_of_month', '*');
                                    $set('cron_month', '*');
                                    $set('cron_day_of_week', '*');
                                }),
                            Action::make('every_x_hours')
                                ->disabled(fn (string $operation) => $operation === 'view')
                                ->form([
                                    TextInput::make('x')
                                        ->label('')
                                        ->numeric()
                                        ->minValue(1)
                                        ->maxValue(24)
                                        ->prefix('Every')
                                        ->suffix('Hours'),
                                ])
                                ->action(function (Set $set, $data) {
                                    $set('cron_minute', '0');
                                    $set('cron_hour', '*/' . $data['x']);
                                    $set('cron_day_of_month', '*');
                                    $set('cron_month', '*');
                                    $set('cron_day_of_week', '*');
                                }),
                            Action::make('every_x_days')
                                ->disabled(fn (string $operation) => $operation === 'view')
                                ->form([
                                    TextInput::make('x')
                                        ->label('')
                                        ->numeric()
                                        ->minValue(1)
                                        ->maxValue(24)
                                        ->prefix('Every')
                                        ->suffix('Days'),
                                ])
                                ->action(function (Set $set, $data) {
                                    $set('cron_minute', '0');
                                    $set('cron_hour', '0');
                                    $set('cron_day_of_month', '*/' . $data['x']);
                                    $set('cron_month', '*');
                                    $set('cron_day_of_week', '*');
                                }),
                            Action::make('every_x_months')
                                ->disabled(fn (string $operation) => $operation === 'view')
                                ->form([
                                    TextInput::make('x')
                                        ->label('')
                                        ->numeric()
                                        ->minValue(1)
                                        ->maxValue(24)
                                        ->prefix('Every')
                                        ->suffix('Months'),
                                ])
                                ->action(function (Set $set, $data) {
                                    $set('cron_minute', '0');
                                    $set('cron_hour', '0');
                                    $set('cron_day_of_month', '0');
                                    $set('cron_month', '*/' . $data['x']);
                                    $set('cron_day_of_week', '*');
                                }),
                            Action::make('every_x_day_of_week')
                                ->disabled(fn (string $operation) => $operation === 'view')
                                ->form([
                                    Select::make('x')
                                        ->label('')
                                        ->prefix('Every')
                                        ->options([
                                            '0' => 'Sunday',
                                            '1' => 'Monday',
                                            '2' => 'Tuesday',
                                            '3' => 'Wednesday',
                                            '4' => 'Thursday',
                                            '5' => 'Friday',
                                            '6' => 'Saturday',
                                        ]),
                                ])
                                ->action(function (Set $set, $data) {
                                    $set('cron_minute', '0');
                                    $set('cron_hour', '0');
                                    $set('cron_day_of_month', '*');
                                    $set('cron_month', '*');
                                    $set('cron_day_of_week', $data['x']);
                                }),
                        ]),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            TasksRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSchedules::route('/'),
            'create' => Pages\CreateSchedule::route('/create'),
            'view' => Pages\ViewSchedule::route('/{record}'),
            'edit' => Pages\EditSchedule::route('/{record}/edit'),
        ];
    }
}