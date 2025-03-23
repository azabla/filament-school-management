<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MarkTypeResource\Pages;
use App\Filament\Resources\MarkTypeResource\RelationManagers;
use App\Models\MarkType;
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

class MarkTypeResource extends Resource
{
    protected static ?string $model = MarkType::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Mark List';


    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            TextInput::make('mark_type')
            ->required()
            ->autofocus(),

            TextInput::make('amount')
            ->numeric() // Ensures the input is a number
            ->minValue(1) // Minimum value is 1
            ->maxValue(100) // Maximum value is 100
            ->required() // Makes the field required
            ->label('Amount'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('mark_type'),
                TextColumn::make('amount')
                ->badge(),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMarkTypes::route('/'),
            'create' => Pages\CreateMarkType::route('/create'),
            'edit' => Pages\EditMarkType::route('/{record}/edit'),
        ];
    }
}