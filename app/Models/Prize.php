<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prize extends Model
{
    use HasFactory;

    protected $fillable = [
        'auction_id',
        'bid_id',
        'is_received',
    ];

    public function auction()
    {
        return $this->belongsTo(Auction::class, 'auction_id');
    }

    public function bid()
    {
        return $this->belongsTo(Bid::class, 'bid_id');
    }
}
