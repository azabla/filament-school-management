<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\Classes;
use App\Models\Section;
use App\Models\Subject;
use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use DB;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables; // Import the correct Tables namespace
use Filament\Tables\Actions\AttachAction;
// use Filament\Actions\CreateAction;
// use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Table; // Import the correct Table class
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Hidden;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;



class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationBadge() : ?string{
        return static::getModel()::count();
    }

    // public static function form(Form $form): Form
    // {
    //     return $form
    //         ->schema([
    //             TextInput::make('name')
    //                 ->required()
    //                 ->autofocus(),
    //             TextInput::make('email')
    //                 ->email()
    //                 ->required()
    //                 ->unique(ignoreRecord: true),

    //             Select::make('roles')
    //                 ->relationship('roles', 'name')
    //                 ->options(
    //                     \Spatie\Permission\Models\Role::pluck('name', 'id')->toArray() // Ensure it only fetches roles
    //                 )
    //                 ->multiple()
    //                 ->preload()
    //                 ->searchable()
    //                 ->required()
    //                 ->reactive()
    //                 ->dehydrated(true)
    //                 ->default([]) // Ensure no roles are pre-selected unless explicitly set
    //                 // ->formatStateUsing(fn ($state) => $state ?? []) // Ensure state initializes as empty
    //                 ->afterStateUpdated(function (callable $set, $state) {
    //                     // Map role IDs to role names
    //                   $roleNames = \Spatie\Permission\Models\Role::whereIn('id', $state)->pluck('name')->toArray();

    //                     // Dynamically show/hide student-specific fields based on roles
    //                     dd($roleNames); // Debug the selected roles
    //                     $set('is_student', in_array('Student', $roleNames));
    //                     $set('is_teacher', in_array('Teacher', $roleNames));
    //                 }),
                    
    //                 Select::make('class_id')
    //                 ->label('Class (for students only)')
    //                 ->relationship('class', 'name')
    //                 ->options(
    //                     Classes::pluck('name', 'id')->toArray() // Ensure it only fetches class names
    //                 )
    //                 ->visible(fn ($get) => $get('is_student'))
    //                 ->dehydrated(true)
    //                 ->required(fn ($get) => $get('is_student')), // Required only for students

    //                 Select::make('section_id')
    //                 ->label('Section (for students only)')
    //                 ->relationship('section', 'name')
    //                 ->options(
    //                     Section::pluck('name', 'id')->toArray() // Ensure it only fetches section names
    //                 )
    //                 ->visible(fn ($get) => $get('is_student'))
    //                 ->dehydrated(true)
    //                 ->required(fn ($get) => $get('is_student')), // Required only for students

    //             // Select::make('class_id')
    //             //     ->label('Class IDs (for teachers only)')
    //             //     ->relationship('classes', 'name')
    //             //     ->options(
    //             //         Classes::pluck('name', 'id')->toArray() // Ensure it only fetches classes
    //             //     )
    //             //     ->multiple()
    //             //     ->preload()
    //             //     ->searchable()
    //             //     ->required(fn ($get) => $get('is_teacher')) // Required only for teachers
    //             //     ->visible(fn ($get) => $get('is_teacher'))
    //             //     ->dehydrated(true)
    //             //     ->default([]) // Ensure no classes are pre-selected unless explicitly set
    //             //     ->formatStateUsing(fn ($state) => $state ?? []), // Ensure state initializes as empty

    //             // Select::make('section_id')
    //             //     ->label('Section IDs (for teachers only)')
    //             //     ->relationship('sections', 'name')
    //             //     ->options(
    //             //         Section::pluck('name', 'id')->toArray() // Ensure it only fetches sections
    //             //     )
    //             //     ->multiple()
    //             //     ->preload()
    //             //     ->searchable()
    //             //     ->required(fn ($get) => $get('is_teacher')) // Required only for teachers
    //             //     ->visible(fn ($get) => $get('is_teacher'))
    //             //     ->dehydrated(true)
    //             //     ->default([]) // Ensure no sections are pre-selected unless explicitly set
    //             //     ->formatStateUsing(fn ($state) => $state ?? []), // Ensure state initializes as empty

    //             //     Select::make('subject_id')
    //             //         ->label('Subjects (for students only)')
    //             //         ->relationship('subjects', 'name')
    //             //         ->options(
    //             //             Subject::pluck('name', 'id')->toArray() // Ensure it only fetches sections
    //             //         )
    //             //         ->multiple()
    //             //         ->preload()
    //             //         ->searchable()
    //             //         ->required(fn ($get) => $get('is_student')) // Required only for students
    //             //         ->visible(fn ($get) => $get('is_student'))
    //             //         ->dehydrated(true)
    //             //         ->default([]) // Ensure no subjects are pre-selected unless explicitly set
    //             //         ->formatStateUsing(fn ($state) => $state ?? []), // Ensure state initializes as empty

    //                     TextInput::make('password')
    //                     ->password() // Toggles password visibility
    //                     ->revealable()
    //                     ->required()
    //                     ->autocomplete('new-password') // Prevents browser from auto-filling
    //                     ->dehydrated(fn ($state) => !empty($state)) // Only save if user enters a value
    //                     ->nullable(),
    //             ]);
            
    // }

    public static function form(Form $form): Form
{
    $isCreate = $form->getOperation() === 'create';
    return $form
        ->schema([
            // User fields
            TextInput::make('name')
                ->required()
                ->autofocus(),
            TextInput::make('email')
                ->email()
                ->required()
                ->unique(ignoreRecord: true),

            // Role selection
            Select::make('roles')
                ->relationship('roles', 'name')
                ->options(\Spatie\Permission\Models\Role::pluck('name', 'id')->toArray())
                ->multiple()
                ->required()
                ->reactive()
                ->dehydrated(fn ($state) => !empty($state)) // Ensure roles are sent if not empty
                ->afterStateUpdated(function (callable $set, $state) {
                    // Map role IDs to role names
                    $roleNames = \Spatie\Permission\Models\Role::whereIn('id', $state)->pluck('name')->toArray();

                    // Dynamically show/hide student-specific fields based on roles
                    $set('is_student', in_array('Student', $roleNames));
                    $set('is_teacher', in_array('Teacher', $roleNames));
                }),

            // Student-specific fields (only visible if the user has the "Student" role)
            Select::make('class_id') // This field will be mapped to the Student model later
                ->label('Class (for students only)')
                ->options(Classes::pluck('name', 'id')->toArray())
                ->visible(fn ($get) => $get('is_student'))
                ->required(fn ($get) => $get('is_student'))
                ->default(null), // Ensure no section is pre-selected unless explicitly set

            Select::make('section_id') // This field will be mapped to the Student model later
                ->label('Section (for students only)')
                ->options(Section::pluck('name', 'id')->toArray())
                ->visible(fn ($get) => $get('is_student'))
                ->required(fn ($get) => $get('is_student'))
                ->default(null), // Ensure no section is pre-selected unless explicitly set

            // Password field
            TextInput::make('password')
            ->password()
            ->revealable()
            // ->required() 
            ->minLength(6)
            ->maxLength(20)
            ->autocomplete('new-password') 
            ->dehydrated(fn ($state) => filled($state))
            ->required(fn (string $context): bool => $context === 'create')
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
                TextColumn::make('roles.name')
                    ->badge(),
                TextColumn::make('created_at')
                    ->date()
                    ->badge(),
                TextColumn::make('updated_at')
                    ->date()
                    ->badge(),
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
            // ->headerActions([
            //     CreateAction::make()
            //     ->using(function (array $data): Model {
            //         // Start a database transaction
            //         DB::beginTransaction();
            //         dd('andu');

            //         try {
            //             // Create the user
            //             $user = User::create([
            //                 'name' => $data['name'],
            //                 'email' => $data['email'],
            //                 'password' => bcrypt($data['password']),
            //             ]);

            //             \Log::debug('User was recently created and foreign key is filled', ['user_id' => $user->id]);
            //             \Log::debug('Data', ['user_id' => $data]);

            //             // Handle post-save logic
            //             $roles = \Spatie\Permission\Models\Role::whereIn('id', $data['roles'])->pluck('name')->toArray();

            //             if (in_array('Student', $roles)) {
            //                 // Create a Student and link it to the user
            //                 $student = Student::create([
            //                     'user_id' => $user->id,
            //                     'class_id' => $data['class_id'] ?? null,
            //                     'section_id' => $data['section_id'] ?? null,
            //                     'name' => $user->name,
            //                     'email' => $user->email,
            //                 ]);

            //                 // Attach subjects to the student via the pivot table
            //                 $subjectIds = $data['subject_id'] ?? [];
            //                 $student->subjects()->attach($subjectIds);

            //                 return $user;
            //             } elseif (in_array('Teacher', $roles)) {
            //                 // Create a Teacher and link it to the user
            //                 Teacher::create([
            //                     'user_id' => $user->id,
            //                     'name' => $user->name,
            //                     'email' => $user->email,
            //                 ]);

            //                 // Attach sections to the teacher via the pivot table
            //                 $sectionIds = $data['teacher_section_id'] ?? [];
            //                 $user->teacher->sections()->attach($sectionIds);

            //                 // Attach classes to the teacher via the pivot table
            //                 $classIds = $data['teacher_class_id'] ?? [];
            //                 $user->teacher->classes()->attach($classIds);
            //             }

            //             // Commit the transaction
            //             DB::commit();

            //             \Log::debug('User was successfully created with relationships', ['user_id' => $user->id]);

            //             // Return the created user model
            //             return $user;
            //         } catch (\Exception $e) {
            //             // Roll back the transaction in case of an error
            //             DB::rollBack();

            //             \Log::error('Error creating user:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            //             throw $e; // Re-throw the exception for Filament to handle
            //         }
            //     }),
            // ]);

    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}