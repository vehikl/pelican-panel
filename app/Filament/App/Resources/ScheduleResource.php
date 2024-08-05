<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\ScheduleResource\Pages;
use App\Filament\App\Resources\ScheduleResource\RelationManagers\TasksRelationManager;
use App\Models\Schedule;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;

class ScheduleResource extends Resource
{
    protected static ?string $model = Schedule::class;

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