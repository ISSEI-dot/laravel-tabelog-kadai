<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\CustomVerifyEmail;
use App\Notifications\CustomResetPassword;
use Overtrue\LaravelFavorite\Traits\Favoriter;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Cashier\Billable;
use Laravel\Cashier\SubscriptionItem;
use Overtrue\LaravelFavorite\Favorite;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, Favoriter, SoftDeletes, Billable;
 
    protected $dates = ['deleted_at'];

    public function sendEmailVerificationNotification()
    {
         $this->notify(new CustomVerifyEmail());
    }

    public function sendPasswordResetNotification($token) {
        $this->notify(new CustomResetPassword($token));
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'postal_code',
        'address',
        'phone'
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
    ];

    public function reviews()
     {
         return $this->hasMany(Review::class);
     }
     
    // お気に入りリレーション
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    // 予約履歴リレーション
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

}
