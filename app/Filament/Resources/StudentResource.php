<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentResource\Pages;
use App\Filament\Resources\StudentResource\RelationManagers;
use App\Models\Student;
use App\Models\Subject;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;
use App\Models\Section;
use App\Models\Classes;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Facades\Auth;


class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Acadamic Management';

    public static function getNavigationBadge() : ?string{ // if role is teacher the count will be only with in his class and section.
        $user = Auth::user();
        
            // Check if the user is a teacher
            if ($user->hasRole('Teacher')) {
                // Eager-load teacher's sections and classes
                $teacher = $user->teacher()->with(['sections', 'classes'])->firstOrFail();
                $teacherSections = $teacher->sections->pluck('id');
                $teacherClasses = $teacher->classes->pluck('id');
        
                // Return filtered query
                return static::getModel()::
                    whereHas('class', function ($query) use ($teacherClasses) {
                        $query->whereIn('id', $teacherClasses);
                    })
                    ->whereHas('section', function ($query) use ($teacherSections) {
                        $query->whereIn('id', $teacherSections);
                    })->count();
            }
    
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
             
                Select::make('class_id')
                // ->live()
                ->relationship(name: 'class', titleAttribute: 'name'),

               Select::make('section_id')
               ->label('section')
               ->relationship(name: 'section', titleAttribute: 'name')
               ->required(),

                TextInput::make('name')
                ->required()
                ->autofocus(),
                TextInput::make('email')
                ->email()
                ->required()
                ->unique(ignoreRecord: true),
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
                TextColumn::make('class.name')
                ->badge(),
                TextColumn::make('section.name')
                ->badge(),

                TextColumn::make('subjects.name')
                ->badge(),
                
            ])
            ->filters([

                Filter::make('class_section_filter')
                ->form([
                    Select::make('class_id')
                    ->label('Filter by class')
                    ->placeholder('Select a class')
                    ->options(
                        Classes::pluck('name', 'id')->toArray()
                    ),

                    Select::make('section_id')
                    ->label('Filter by section')
                    ->placeholder('Select a section')
                    ->options(
                        Section::pluck('name', 'id')->toArray()
                    )
                ])
                ->query(function (Builder $query , array $data): Builder {
                   return $query->when($data['class_id'], function ($query) use($data) {
                       return  $query->where('class_id', $data['class_id']);
                    })->when($data['section_id'], function ($query) use($data) {
                        return $query->where('section_id', $data['section_id']);
                    });
                })
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
                ])
                ->action(function (Student $user, array $data): void {

                       $user->subjects()->attach($data['subjectId']);
               })
               ->label('Attach')
               ->icon('heroicon-o-plus')
               ->color('primary')
               ->visible(fn () => Auth::user()->hasRole('Admin') || Auth::user()->hasRole('super_admin')),

                // Detach Action
            DetachAction::make('detachSubjects')
            ->label('Detach Subjects')
            ->requiresConfirmation() // Prompt the user for confirmation before detaching
            ->form(fn (DetachAction $action): array => [
                Select::make('subjectId')
                    ->label('Subjects')
                    ->options(Subject::all()->pluck('name', 'id'))
                    ->multiple(),
            ])
            ->action(function (Student $user, array $data): void {
                if (Auth::user()->hasRole(['super_admin', 'Admin'])) {
                    // Detach subjects
                    if (!empty($data['subjectId'])) {
                        $user->subjects()->detach($data['subjectId']);
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

    public static function getEloquentQuery(): Builder
{
    $user = Auth::user();

    // Check if the user is a teacher
    if ($user->hasRole('Teacher')) {
        // Eager-load teacher's sections and classes
        $teacher = $user->teacher()->with(['sections', 'classes'])->firstOrFail();
        $teacherSections = $teacher->sections->pluck('id');
        $teacherClasses = $teacher->classes->pluck('id');

        // Return filtered query
        return parent::getEloquentQuery()
            ->whereHas('class', function ($query) use ($teacherClasses) {
                $query->whereIn('id', $teacherClasses);
            })
            ->whereHas('section', function ($query) use ($teacherSections) {
                $query->whereIn('id', $teacherSections);
            });
    }

    // For other roles, return all students
    return parent::getEloquentQuery();
}
    
    

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
            // 'create' => Pages\CreateStudent::route('/create'),
            // 'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }
}