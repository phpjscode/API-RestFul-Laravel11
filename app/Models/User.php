<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes;

    const USUARIO_VERIFICADO = '1';
    const USUARIO_NO_VERIFICADO = '0';

    const USUARIO_ADMINISTRADOR = 'true';
    const USUARIO_REGULAR = 'false';

    /**
     * The table associated with the model.
     *
     * @var string|null
     */
    protected $table = 'users';
    // protected $dates = [ // En Laravel 7<
    //     'deleted_at',
    // ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'verified',
        'verification_token',
        'admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'verification_token',
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
            'deleted_at' => 'datetime',
        ];
    }

    public function esVerificado()
    {
        return $this->verified == User::USUARIO_VERIFICADO;
    } 

    public function esAdministrador()
    {
        return $this->admin == User::USUARIO_ADMINISTRADOR;
    }

    public static function generarVerificationToken()
    {
        // return str_random(40); // Laravel < 6.0
        return Str::random(40); 
    }

    public function name(): Attribute
    {
        return Attribute::make(
            get: fn (string $value, array $attributes) => $attributes['name'] = ucwords($value),
            set: fn (string $value, array $attributes) => $attributes['name'] = strtolower($value)
            // get: fn (string $value) => ucwords($value),
            // set: fn (string $value) => strtolower($value)
        );
    }

    public function email(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => strtolower($value),
        );
    }

    // public function name(): Attribute
    // {
    //     return new Attribute(
    //         get: fn ($value, $attributes) => ucwords($value),
    //         // set: fn ($value, $attributes) => strtolower($value)
    //         set: fn ($value, $attributes) => $attributes['name'] = strtolower($value)
    //     );
    // }

    // public function email(): Attribute
    // {
    //     return new Attribute(
    //         // set: fn ($value, $attributes) => strtolower($value),
    //         set: fn ($value, $attributes) => $attributes['email'] = strtolower($value)
    //     );
    // }

    // public function name(): Attribute
    // {
    //     return new Attribute(
    //         get: function ($value, $attributes) {
    //             return ucwords($value);
    //         },
    //         set: function ($value, $attributes) {
    //             // return strtolower($value);
    //             return $attributes['name'] = strtolower($value);
    //         }
    //     );
    // }

    // public function email(): Attribute
    // {
    //     return new Attribute(
    //         set: function ($value, $attributes) {
    //             // return strtolower($value);
    //             return $attributes['email'] = strtolower($value);
    //         }

    //     );
    // }

    // public function getNameAttribute($value)
    // {
    //     return ucwords($value);
    // }

    // public function setNameAttribute($value)
    // {
    //     $this->attributes['name'] = strtolower($value);
    // } 

    // public function setEmailAttribute($value)
    // {
    //     $this->attributes['email'] = strtolower($value);
    // }
}
