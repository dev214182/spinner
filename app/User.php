<?php

namespace App;

use App\Company;
use App\Product;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Policies\HasPermissionsTrait;

class User extends Authenticatable
{
    use Notifiable;
    use HasPermissionsTrait; //Import The Trait

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'role', 'phone', 'company_id', 'status'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Setup Connections
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * User Roles
     * 1. Super Admin
     * 2. App Admin
     * 3. Team Admin
     * 4. Team Editor
     */
}
