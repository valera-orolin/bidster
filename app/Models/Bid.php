<?php

namespace App\Models;

use App\Models\User;
use App\Models\Auction;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bid extends Model
{
    use HasFactory;

    protected $fillable = [
        'bid_size',
        'lot_id',
        'user_id',
    ];

    public function auction()
    {
        return $this->belongsTo(Auction::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function getBidsByHour($date)
    {
        $query = self::whereDate('created_at', $date);

        $query->select(DB::raw('HOUR(created_at) as hour'), DB::raw('count(*) as count'))
            ->groupBy(DB::raw('HOUR(created_at)'));

        return $query->get()
                    ->pluck('count', 'hour')
                    ->toArray();
    }

    public static function getBidsByDay($startDate, $endDate)
    {
        $query = self::whereBetween('created_at', [$startDate, $endDate]);

        $query->select(DB::raw('DATE(created_at) as day'), DB::raw('count(*) as count'))
            ->groupBy(DB::raw('DATE(created_at)'));

        return $query->get()
                    ->pluck('count', 'day')
                    ->toArray();
    }

    public static function getBidSumByHour($date)
    {
        $query = self::whereDate('created_at', $date);

        $query->select(DB::raw('HOUR(created_at) as hour'), DB::raw('SUM(bid_size) as sum'))
            ->groupBy(DB::raw('HOUR(created_at)'));

        return $query->get()
                    ->pluck('sum', 'hour')
                    ->toArray();
    }

    public static function getBidSumByDay($startDate, $endDate)
    {
        $query = self::whereBetween('created_at', [$startDate, $endDate]);

        $query->select(DB::raw('DATE(created_at) as day'), DB::raw('SUM(bid_size) as sum'))
            ->groupBy(DB::raw('DATE(created_at)'));

        return $query->get()
                    ->pluck('sum', 'day')
                    ->toArray();
    }
}
