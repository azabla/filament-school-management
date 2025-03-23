<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Panel;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    use HasPanelShield;

    // protected $guard_name = 'web';

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasRole(['super-admin', 'Admin', 'Student', 'Teacher']);
    }
    // public function roles()
    // {
    //     return $this->belongsToMany(\Spatie\Permission\Models\Role::class);
    // }
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */


    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relationship with Student (assuming each User is either a Student or Teacher)
    public function student()
    {
        return $this->hasOne(Student::class, 'user_id');  // assuming each user has one student record
    }

    public function teacher()
    {
        return $this->hasOne(Teacher::class, 'user_id');  // if you have a Teacher table
    }

}