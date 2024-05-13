<?php

namespace App\Models;

use App\Models\Lot;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Auction extends Model
{
    use HasFactory;

    const STATUS_ACTIVE = 'Active';
    const STATUS_FINISHED = 'Finished';
    const STATUS_FAILED = 'Failed';

    protected $fillable = [
        'status',
        'lot_id',
        'seller_id',
    ];

    public function lot()
    {
        return $this->belongsTo(Lot::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
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

    public function isLikedByUser()
    {
        return $this->likes()->where('user_id', auth()->id())->exists();
    }

    public static function getAuctionsByHour($date, $status)
    {
        $query = self::whereDate('updated_at', $date)
                    ->where('status', $status);

        $query->select(DB::raw('HOUR(updated_at) as hour'), DB::raw('count(*) as count'))
        ->groupBy(DB::raw('HOUR(updated_at)'));

        return $query->get()
                    ->pluck('count', 'hour')
                    ->toArray();
    }

    public static function getAuctionsByDay($startDate, $endDate, $status)
    {
        $query = self::whereBetween('updated_at', [$startDate, $endDate])
                    ->where('status', $status);

        $query->select(DB::raw('DATE(updated_at) as day'), DB::raw('count(*) as count'))
            ->groupBy(DB::raw('DATE(updated_at)'));

        return $query->get()
                    ->pluck('count', 'day')
                    ->toArray();
    }

    public static function getMaxBidSumByHour($date)
    {
        $query = self::whereDate('auctions.updated_at', $date)
                    ->where('status', self::STATUS_FINISHED);

        $query->join('bids', 'auctions.id', '=', 'bids.auction_id')
            ->select(DB::raw('HOUR(auctions.updated_at) as hour'), DB::raw('MAX(bids.bid_size) as max_bid'))
            ->groupBy(DB::raw('HOUR(auctions.updated_at)'));

        return $query->get()
                    ->pluck('max_bid', 'hour')
                    ->toArray();
    }

    public static function getMaxBidSumByDay($startDate, $endDate)
    {
        $query = self::whereBetween('auctions.updated_at', [$startDate, $endDate])
                    ->where('status', self::STATUS_FINISHED);

        $query->join('bids', 'auctions.id', '=', 'bids.auction_id')
            ->select(DB::raw('DATE(auctions.updated_at) as day'), DB::raw('MAX(bids.bid_size) as max_bid'))
            ->groupBy(DB::raw('DATE(auctions.updated_at)'));

        return $query->get()
                    ->pluck('max_bid', 'day')
                    ->toArray();
    }

    public static function getAuctionCount($startDate, $endDate = null, $status = null)
    {
        $query = self::query();

        if ($status != null) {
            $query->where('status', $status);
        }

        if ($endDate == null) {
            $query->whereDate('updated_at', $startDate);
        } else {
            $query->whereBetween('updated_at', [$startDate, $endDate]);
        }

        return $query->count();
    }
}
