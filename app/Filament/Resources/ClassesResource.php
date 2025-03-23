<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClassesResource\Pages;
use App\Filament\Resources\ClassesResource\RelationManagers;
use App\Models\Classes;
use App\Models\Section;
use App\Models\Student;
use App\Models\Subject;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Auth;


class ClassesResource extends Resource
{
    protected static ?string $model = Classes::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Class Management';


    public static function getNavigationBadge() : ?string {

        $user = Auth::user();

        if ($user->hasRole('Admin') || $user->hasRole('super_admin')) {
            return static::getModel()::count();
        }

        return static::getModel()::whereHas('teachers', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->count();
        
    }
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
               TextColumn::make('name'),
               TextColumn::make('sections.name')
               ->badge(),
               TextColumn::make('students_count')
               ->counts('students')
               ->label('Students')
               ->badge(),

            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),

                // Add other actions if needed
                AttachAction::make('sections')
                ->multiple() // Allow attaching multiple subjects
                ->preloadRecordSelect() // Preload the options for better performance
                ->form(fn (AttachAction $action): array => [
                      Select::make('sectionId') // 'recordId' is the key for the related model ID
                        ->label('Sections')
                        ->options(Section::all()->pluck('name', 'id')) // Fetch subjects and map to name => id
                        ->multiple(), // Allow selecting multiple subjects
                ])
                ->action(function (Classes $user, array $data): void {

                       $user->sections()->attach($data['sectionId']);
               })
               ->label('Attach')
               ->icon('heroicon-o-plus')
               ->color('secondary')
               ->visible(fn () => Auth::user()->hasRole('Admin') || Auth::user()->hasRole('super_admin')),

                // Detach Action
                DetachAction::make('detachSections')
                ->label('Detach Sections')
                ->requiresConfirmation() // Prompt the user for confirmation before detaching
                ->form(fn (DetachAction $action): array => [
                    
                    Select::make('sectionId')
                        ->label('Sections')
                        ->options(Section::all()->pluck('name', 'id'))
                        ->multiple(),
                ])
                ->action(function (Classes $user, array $data): void {
                    if (Auth::user()->hasRole(['super_admin', 'Admin'])) {
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

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();

        // Check if the user has the 'Teacher' role
        if ($user->hasRole('Teacher')) {
            // Get the teacher associated with the logged-in user
            return parent::getEloquentQuery()
                ->whereHas('teachers', function ($query) use ($user) {
                    $query->where('user_id', $user->id); // Filter sections that the teacher belongs to
                });
        }

        // For other roles or users, return all sections
        return parent::getEloquentQuery();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClasses::route('/'),
            'create' => Pages\CreateClasses::route('/create'),
            'edit' => Pages\EditClasses::route('/{record}/edit'),
        ];
    }
}