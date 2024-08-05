<?php

namespace App\Filament\App\Resources\ScheduleResource\RelationManagers;

use App\Models\Schedule;
use App\Models\Task;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;

class TasksRelationManager extends RelationManager
{
    protected static string $relationship = 'tasks';

    public function table(Table $table): Table
    {
        /** @var Schedule $schedule */
        $schedule = $this->getOwnerRecord();

        return $table
            ->reorderable('sequence_id', true)
            ->columns([
                TextColumn::make('action'),
                TextColumn::make('time_offset')
                    ->hidden(fn () => config('queue.default') === 'sync')
                    ->suffix(' Seconds'),
                IconColumn::make('continue_on_failure')
                    ->boolean(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Create Task')
                    ->createAnother(false)
                    ->form([
                        TextInput::make('sequence_id')
                            ->hidden()
                            ->default(fn () => ($schedule->tasks()->orderByDesc('sequence_id')->first()->sequence_id ?? 0) + 1),
                        Select::make('action')
                            ->required()
                            ->live()
                            ->options([
                                Task::ACTION_POWER => 'Send power action',
                                Task::ACTION_COMMAND => 'Send command',
                                Task::ACTION_BACKUP => 'Create backup',
                                Task::ACTION_DELETE_FILES => 'Delete files',
                            ]),
                        Textarea::make('payload')
                            ->hidden(fn (Get $get) => $get('action') === Task::ACTION_POWER)
                            ->label(fn (Get $get) => match ($get('action')) {
                                Task::ACTION_POWER => 'Power action',
                                Task::ACTION_COMMAND => 'Command',
                                Task::ACTION_BACKUP => 'Files to ignore',
                                Task::ACTION_DELETE_FILES => 'Files to delete',
                                default => 'Payload'
                            }),
                        Select::make('payload')
                            ->visible(fn (Get $get) => $get('action') === Task::ACTION_POWER)
                            ->label('Power Action')
                            ->required()
                            ->options([
                                'start' => 'Start',
                                'restart' => 'Restart',
                                'stop' => 'Stop',
                                'Kill' => 'Kill',
                            ]),
                        TextInput::make('time_offset')
                            ->hidden(fn (Get $get) => config('queue.default') === 'sync' || $get('sequence_id') === 1)
                            ->default(0)
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(900)
                            ->suffix('Seconds'),
                        Toggle::make('continue_on_failure'),
                    ]),
            ]);
    }
}