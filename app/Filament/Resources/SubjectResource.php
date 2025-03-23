<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubjectResource\Pages;
use App\Filament\Resources\SubjectResource\RelationManagers;
use App\Models\Subject;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Auth;

class SubjectResource extends Resource
{
    protected static ?string $model = Subject::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationGroup = 'Mark List';
    

    public static function getNavigationBadge(): ?string {
        $user = Auth::user();

        if ($user->hasRole('Admin') || $user->hasRole('super_admin')) {
            return static::getModel()::count();
        }

        if ($user->hasRole('Student')) {
            return static::getModel()::whereHas('students', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->count();
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
                    ->label('Name')
                    ->required()
                    ->unique(ignoreRecord: true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                ->visible(fn () => Auth::user()->hasRole(['Admin', 'super_admin'])), // Only visible to Admins,
                Tables\Actions\DeleteAction::make()
                ->visible(fn () => Auth::user()->hasRole(['Admin', 'super_admin'])), // Only visible to Admins,
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
        
        if ($user->hasRole('Teacher')) {
     
            return parent::getEloquentQuery()
                ->whereHas('teachers', function ($query) use ($user) {
                    $query->where('user_id', $user->id); 
                });
        }
          
          if ($user->hasRole('Student')) {
            
            return parent::getEloquentQuery()
                ->whereHas('students', function ($query) use ($user) {
                    $query->where('user_id', $user->id); 
                });
        }
       
        return parent::getEloquentQuery();
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubjects::route('/'),
            'create' => Pages\CreateSubject::route('/create'),
            'edit' => Pages\EditSubject::route('/{record}/edit'),
        ];
    }
}