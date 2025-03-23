<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeacherResource\Pages;
use App\Filament\Resources\TeacherResource\RelationManagers;
use App\Models\Classes;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;

class TeacherResource extends Resource
{
    protected static ?string $model = Teacher::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Acadamic Management';

    public static function getNavigationBadge() : ?string{
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                ->required()
                ->autofocus(),
                TextInput::make('email')
                ->email()
                ->required()
                ->unique(ignoreRecord: true)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                ->searchable()
                ->sortable(),
                TextColumn::make('email')
                ->searchable()
                ->sortable(),

                TextColumn::make('subjects.name')
                ->badge(),

                TextColumn::make('classes.name')
                ->badge(),
                TextColumn::make('sections.name')
                ->badge(),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),

                // Add other actions if needed
                AttachAction::make('subjects')
                ->multiple() // Allow attaching multiple subjects
                ->preloadRecordSelect() // Preload the options for better performance
                ->form(fn (AttachAction $action): array => [
                      Select::make('subjectId') // 'recordId' is the key for the related model ID
                        ->label('Subjects')
                        ->options(Subject::all()->pluck('name', 'id')) // Fetch subjects and map to name => id
                        ->multiple(), // Allow selecting multiple subjects
                        

                        Select::make('classesId') // 'recordId' is the key for the related model ID
                        ->label('Classes')
                        ->options(Classes::all()->pluck('name', 'id')) // Fetch subjects and map to name => id
                        ->multiple(), // Allow selecting multiple subjects
                        

                        Select::make('sectionId') // 'recordId' is the key for the related model ID
                        ->label('Sections')
                        ->options(Section::all()->pluck('name', 'id')) // Fetch subjects and map to name => id
                        ->multiple(),// Allow selecting multiple subjects

                        
                ])
                ->action(function (Teacher $user, array $data): void {

                       $user->subjects()->attach($data['subjectId']);
                       $user->sections()->attach($data['classesId']);
                       $user->classes()->attach($data['sectionId']);
               })
               ->label('Attach')
               ->icon('heroicon-o-plus')
               ->color('primary')
               ->visible(fn () => Auth::user()->hasRole('Admin') || Auth::user()->hasRole('super_admin')),


            // Detach Action
            DetachAction::make('detachSubjects')
                ->label('Detach Subjects, Classes, and Sections')
                ->requiresConfirmation() // Prompt the user for confirmation before detaching
                ->form(fn (DetachAction $action): array => [
                    Select::make('subjectId')
                        ->label('Subjects')
                        ->options(Subject::all()->pluck('name', 'id'))
                        ->multiple(),

                    Select::make('classesId')
                        ->label('Classes')
                        ->options(Classes::all()->pluck('name', 'id'))
                        ->multiple(),

                    Select::make('sectionId')
                        ->label('Sections')
                        ->options(Section::all()->pluck('name', 'id'))
                        ->multiple()
                ])
                ->action(function (Teacher $user, array $data): void {
                    if ($user->user->hasRole(['super_admin', 'Admin', 'Teacher'])) {
                        // Detach subjects
                        if (!empty($data['subjectId'])) {
                            $user->subjects()->detach($data['subjectId']);
                        }
                        // Detach classes
                        if (!empty($data['classesId'])) {
                            $user->classes()->detach($data['classesId']);
                        }

                        // Detach sections
                        if (!empty($data['sectionId'])) {
                            $user->sections()->detach($data['sectionId']);
                        }
                    }
                })
                ->label('Detach')
                ->icon('heroicon-o-minus')
                ->color('danger')
               ->visible(fn () => Auth::user()->hasRole('Admin') || Auth::user()->hasRole('super_admin')),


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

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTeachers::route('/'),
            // 'create' => Pages\CreateTeacher::route('/create'),
            // 'edit' => Pages\EditTeacher::route('/{record}/edit'),
        ];
    }
}