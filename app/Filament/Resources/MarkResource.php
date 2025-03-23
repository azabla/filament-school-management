<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MarkResource\Pages;
use App\Filament\Resources\MarkResource\RelationManagers;
use App\Models\Mark;
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
use Filament\Forms\Get;
use App\Models\Student;
use App\Models\MarkType;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Facades\Auth;


class MarkResource extends Resource
{
    protected static ?string $model = Mark::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

        protected static ?string $navigationGroup = 'Mark List';


    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Select::make('student_id')
            ->live()
            ->options(function () {
            $user = Auth::user();
            if ($user->hasRole('Teacher')) {
                $teacherSections = $user->teacher->sections->pluck('id');
                $teacherClasses = $user->teacher->classes->pluck('id');
                return Student::whereIn('class_id', $teacherClasses)
                ->whereHas('section', function ($query) use ($teacherSections) {
                    $query->whereIn('section_id', $teacherSections);
                })
                ->pluck('name', 'id')
                ->toArray();
            }
            return [];
            })
            ->label('Student'),
            
           Select::make('subject_id')
           ->label('Subject')
           ->options(function (Get $get) {
            $studentId = $get('student_id');
            
            if ($studentId) {
                // Retrieve the student with their subjects
                $student = Student::with('subjects')->find($studentId); // Load subjects relation
                if ($student) {
                    return $student->subjects->pluck('name', 'id')->toArray();
                }
            }
        
            return []; // Return an empty array if no student is selected
            })
            ->required(),

            Select::make('mark_type_id')
                ->live()
                ->relationship(name: 'MarkType', titleAttribute: 'mark_type')
                ->required(),

            TextInput::make('mark')
            ->numeric() // Ensures the input is a number
            ->minValue(1) // Minimum value is 1
            ->maxValue(fn (Get $get) => MarkType::find($get('mark_type_id'))?->amount ?? 0) // Restrict to max_amount from MarkType
            ->required() // Makes the field required
            ->label('Result')
            ->autofocus(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.name')
                ->searchable()
                ->sortable(),
                TextColumn::make('subject.name')
                ->searchable()
                ->sortable(),
                TextColumn::make('markType.mark_type')
                ->label('Mark Type')
                ->searchable()
                ->sortable(),
                TextColumn::make('markType.amount')
                ->label('Amount')
                ->searchable()
                ->sortable(),
                TextColumn::make('mark')
                ->searchable()
                ->sortable()
                ->badge(),
            ])
            ->filters([
                Filter::make('mark_filter')
                ->form([
                    Select::make('student_id')
                    ->label('Filter by student')
                    ->placeholder('Select a student')
                    ->options(
                        Student::pluck('name', 'id')->toArray()
                    ),

                    Select::make('subject_id')
                    ->label('Filter by subject')
                    ->placeholder('Select a subject')
                    ->options(function (Get $get) {
                        $stu_id = $get('student_id');
                        if ($stu_id) {
                            // Retrieve the student with their subjects
                            $student = Student::find($stu_id);
                            if ($student) {
                                return $student->subjects()->pluck('name', 'subject_id')->toArray();
                            }
                        }
                        return []; // Return an empty array if no student is selected
                    }),

                    Select::make('mark_type_id')
                    ->label('Filter by Mark Type')
                    ->placeholder('Select a Mark Type')
                    ->options(
                        MarkType::pluck('mark_type', 'id')->toArray()
                    ),
                    

                    Select::make('mark')
                    ->label('Filter by Mark')
                    ->placeholder('Select a Mark')
                    ->options(
                        Mark::pluck('mark', 'id')->toArray()
                    ),
                ])
                ->query(function (Builder $query , array $data): Builder {
                   return $query->when($data['student_id'], function ($query) use($data) {
                       return  $query->where('student_id', $data['student_id']);
                    })->when($data['subject_id'], function ($query) use($data) {
                        return $query->where('subject_id', $data['subject_id']);
                    })->when($data['mark_type_id'], function ($query) use($data) {
                        return $query->where('mark_type_id', $data['mark_type_id']);
                    })->when($data['mark'], function ($query) use($data) {
                        return $query->where('mark', $data['mark']);
                    });
                })
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
    // dd(Auth::guard()->user());
    // \Log::info('Executing getEloquentQuery');

    $user = Auth::user();

    if ($user->hasRole('Student')) {
        return parent::getEloquentQuery()
            ->whereHas('student', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            });
    } else if ($user->hasRole('Teacher')) {
        $teacherSections = $user->teacher->sections->pluck('id');
        $teacherClasses = $user->teacher->classes->pluck('id');
        
        return parent::getEloquentQuery()
            ->whereHas('student', function ($query) use ($teacherSections, $teacherClasses) {
                $query->whereIn('class_id', $teacherClasses)
                      ->whereHas('section', function ($query) use ($teacherSections) {
                          $query->whereIn('section_id', $teacherSections);
                      });
            });
    }

    return parent::getEloquentQuery();
}

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMarks::route('/'),
            'create' => Pages\CreateMark::route('/create'),
            'edit' => Pages\EditMark::route('/{record}/edit'),
        ];
    }
}