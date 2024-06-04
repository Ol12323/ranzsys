<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Support\Facades\Storage;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements FilamentUser, HasName, HasAvatar, MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    public function getFilamentName(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

     
    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return (auth()->user()->role->name === 'Owner' || auth()->user()->role->name === 'Staff' && !auth()->user()->is_banned);
        }elseif ($panel->getId() === 'customer') {
            return (auth()->user()->role->name === 'Customer' && !auth()->user()->is_banned);
        }
 
        
    }

    public function getFilamentAvatarUrl(): ?string {
        return Storage::url($this->avatar);
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'last_name',
        'first_name',
        'avatar',
        'phone_number',
        'date_of_birth',
        'address',
        'role_id',
        'email',
        'password',
        'is_banned',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the role that owns the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function getFullNameAttribute()
    {
        return $this->last_name . ','.' ' . $this->first_name;
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function inbox()
    {
        return $this->hasMany(Message::class, 'recipient_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    
    
}