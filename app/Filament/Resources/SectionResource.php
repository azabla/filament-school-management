<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SectionResource\Pages;
use App\Filament\Resources\SectionResource\RelationManagers;
use App\Models\Section;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;



class SectionResource extends Resource
{
    protected static ?string $model = Section::class;

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
            //    Select::make('class_id')
            //    ->relationship(name: 'class', titleAttribute: 'name'),
                TextInput::make('name')
                ->required()
                ->unique(ignoreRecord: true),
                // ->unique(ignoreRecord: true, modfiyRuleUsing:function(Get $get, Unique $rule){
                //     return $rule->where('name', $get('class_id'));
                // }
                // )

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
             TextColumn::make('name'),
             TextColumn::make('students_count')
               ->counts('students')
               ->label('Students')
               ->badge(),
            //  TextColumn::make('class.name')
            //  ->badge(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListSections::route('/'),
            'create' => Pages\CreateSection::route('/create'),
            'edit' => Pages\EditSection::route('/{record}/edit'),
        ];
    }
}