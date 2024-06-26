<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Auction;
use App\Models\AuctionRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'contract_address',
        'description',
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

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($user) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $user->avatar));
        });
    }

    public function auctions()
    {
        return $this->hasMany(Auction::class, 'seller_id');
    }

    public function bids()
    {
        return $this->hasMany(Bid::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function isAdmin()
    {
        return $this->role == 'Director' || $this->role == 'Manager';
    }

    public function isAddressCorrect($address)
    {
        if ($this->contract_address == null) {
            return true;
        }

        return Hash::check($address, $this->contract_address);
    }
}
