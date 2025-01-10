<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\TaskResource\Pages;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(
                array_merge(
                    self::getFormSchema(),
                    [
                        Select::make('project_id')
                            ->label('Project')
                            ->options(Project::query()->pluck('name', 'id'))
                            ->searchable()
                            ->required()
                            ->rules('required|exists:users,id'),
                    ]
                )
            );
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('project.name')
                    ->label('Project')
                    ->limit(30),
                TextColumn::make('user.name')
                    ->label('User'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        Task::STATUSES[0] => 'gray',
                        Task::STATUSES[1] => 'primary',
                        Task::STATUSES[2] => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('description')
                    ->limit(50),
                TextColumn::make('start_date')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Not Set'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }

    /**
     * @return array<int, mixed>
     */
    public static function getFormSchema(): array
    {
        $options = Task::STATUSES;
        $keyValueOptions = array_combine($options, $options);

        return [
            Grid::make(1)->schema(
                [
                    TextInput::make('name')
                        ->label('Name')
                        ->required()
                        ->minLength(3)
                        ->maxLength(255)
                        ->placeholder('Enter the task name')
                        ->rules('required|string|min:3|max:255'),
                    Textarea::make('description')
                        ->label('Description')
                        ->placeholder('Enter the task description'),
                ]
            ),
            Grid::make(2)->schema(
                [
                    DatePicker::make('start_date')
                        ->label('Start Date')
                        ->required()
                        ->placeholder('Enter the start date')
                        ->default(now())
                        ->rules('required|date'),
                    DatePicker::make('end_date')
                        ->label('End Date')
                        ->after('start_date')
                        ->placeholder('Enter the end date')
                        ->reactive()
                        ->nullable()
                        ->rules('required|date|after:start_date'),
                    Select::make('status')
                        ->options($keyValueOptions)
                        ->default($keyValueOptions[$options[0]])
                        ->required()
                        ->rules('required|in:' . implode(',', array_keys(Task::STATUSES))),
                    Select::make('user_id')
                        ->label('User')
                        ->options(User::query()->pluck('name', 'id'))
                        ->searchable()
                        ->required()
                        ->rules('required|exists:users,id'),
                ]
            ),
        ];
    }
}
