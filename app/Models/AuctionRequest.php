<?php

namespace App\Models;

use App\Models\Lot;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AuctionRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'lot_id',
        'old_lot_id',
        'user_id',
        'type'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lot()
    {
        return $this->belongsTo(Lot::class, 'lot_id');
    }

    public function old_lot()
    {
        return $this->belongsTo(Lot::class, 'old_lot_id');
    }
}
