<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\TaskResource\Pages;
use App\Filament\Resources\TaskResource\RelationManagers;
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
                            ->options(Project::all()->pluck('name', 'id'))
                            ->searchable()
                            ->required()
                    ]
                )
            );
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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

    public static function getFormSchema(): array
    {
        return [
            Grid::make(1)->schema(
                    [
                        TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->minLength(3)
                            ->maxLength(255)
                            ->placeholder('Enter the task name'),
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
                        ->default(now()),
                    DatePicker::make('end_date')
                        ->label('End Date')
                        ->after('start_date')
                        ->placeholder('Enter the end date')
                        ->reactive()
                        ->nullable(),
                    Select::make('status')
                        ->options(Task::STATUSES)
                        ->default(Task::STATUSES[0])
                        ->required(),
                    Select::make('user_id')
                        ->label('User')
                        ->options(User::all()->pluck('name', 'id'))
                        ->searchable()
                        ->required(),
                ]
            )
        ];
    }
}
