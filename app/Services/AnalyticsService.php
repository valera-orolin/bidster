<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Bid;
use App\Models\Auction;

class AnalyticsService
{
    private $tab;
    private $from;
    private $to;

    private $day;
    private $startDate;
    private $endDate;
    private $days;

    private $finished_auctions;
    private $placed_bids;
    private $bid_amount;
    private $winning_bid_amount;
    private $completed_auctions_count;

    private $keys;

    private function isPersonalRangeSet() {
        return $this->from && $this->to;
    }

    private function isPersonalRangeOneDay() {
        return $this->from->format('d.m.y') === $this->to->format('d.m.y');
    }

    private function calculateDay() 
    {
        $day = Carbon::today();
        if ($this->tab == 'yesterday') {
            $day = Carbon::yesterday();
        } else if ($this->tab == 'personal' && !$this->isPersonalRangeSet()) {
            $day = Carbon::today();
        } else if ($this->tab == 'personal' && $this->isPersonalRangeOneDay()) {
            $day = $this->from;
        }
        return $day;
    }

    private function calculateStep($tab, &$days, &$endDate) 
    {
        $step = 1;
        if ($tab == 'personal' && $days > 10) {
            for ($i = 6; $i <= 10; $i++) {
                if ($days % $i === 0) {
                    $step = $days / $i;
                    break;
                }
                if ($i == 10) {
                    $days++;
                    $endDate->addDay();
                    $i = 5;
                }
            }
        }
        return $step;
    }

    private function calculateStartDate()
    {
        $startDate = Carbon::today();
        if ($this->tab == 'week') {
            $startDate = Carbon::today()->subDays(6);
        } else if ($this->tab == 'month') {
            $startDate = Carbon::today()->subDays(29);
        } else if ($this->tab == 'personal' && $this->isPersonalRangeSet()) {
            $startDate = $this->from;
        }
        return $startDate->startOfDay();
    }

    private function calculateDays()
    {
        $days = 0;
        if ($this->tab == 'week') {
            $days = 7;
        } else if ($this->tab == 'month') {
            $days = 30;
        } else if ($this->tab == 'personal' && $this->isPersonalRangeSet()) {
            $days = $this->from->diffInDays($this->to) + 1;
        }
        return $days;
    }

    private function calculateEndDate()
    {
        $endDate = Carbon::today();
        if ($this->tab == 'personal' && $this->isPersonalRangeSet()) {
            $endDate = $this->to;
        }
        return $endDate->endOfDay();
    }
    
    public function __construct($tab, $from, $to) 
    {
        $this->tab = $tab;
        $this->from = $from;
        $this->to = $to;
        $this->day = $this->calculateDay();
        $this->startDate = $this->calculateStartDate();
        $this->days = $this->calculateDays();
        $this->endDate = $this->calculateEndDate();
        $this->calculateAnalytics();
    }

    
    private function isOneDay()
    {
        return ($this->tab == 'today' || $this->tab == 'yesterday' || 
            ($this->tab == 'personal' && !$this->isPersonalRangeSet()) ||
            ($this->tab == 'personal' && $this->isPersonalRangeOneDay()));
    }

    private function isRangeOfDays()
    {
        return ($this->tab == 'week' || 
            $this->tab == 'month' || 
            ($this->tab == 'personal' && $this->isPersonalRangeSet()));
    }

    private function calculateAnalyticsForDay()
    {
        $this->keys = range(0, 24);

        $this->finished_auctions = array_fill(0, 24, 0);
        $this->placed_bids = array_fill(0, 24, 0);
        $this->bid_amount = array_fill(0, 24, 0);
        $this->winning_bid_amount = array_fill(0, 24, 0);

        $this->fillDataByHour($this->finished_auctions, Auction::getAuctionsByHour($this->day, Auction::STATUS_FINISHED));
        $this->fillDataByHour($this->placed_bids, Bid::getBidsByHour($this->day));
        $this->fillDataByHour($this->bid_amount, Bid::getBidSumByHour($this->day));
        $this->fillDataByHour($this->winning_bid_amount, Auction::getMaxBidSumByHour($this->day));

        $this->completed_auctions_count[0]['name'] = Auction::STATUS_FINISHED;
        $this->completed_auctions_count[0]['value'] = Auction::getAuctionCount($this->day, null, Auction::STATUS_FINISHED);
        $this->completed_auctions_count[1]['name'] = Auction::STATUS_FAILED;
        $this->completed_auctions_count[1]['value'] = Auction::getAuctionCount($this->day, null, Auction::STATUS_FAILED);
    }

    private function fillDataByHour(&$data, $items)
    {
        foreach ($items as $hour => $count) {
            $data[$hour] = $count;
        }
    }

    
    private function calculateAnalyticsForRange()
    {
        $finished_auctions_raw = Auction::getAuctionsByDay($this->startDate, $this->endDate, Auction::STATUS_FINISHED);
        $placed_bids_raw = Bid::getBidsByDay($this->startDate, $this->endDate);
        $bid_amount_raw = Bid::getBidSumByDay($this->startDate, $this->endDate);
        $winning_bid_amount_raw = Auction::getMaxBidSumByDay($this->startDate, $this->endDate);
        $this->completed_auctions_count[0]['name'] = Auction::STATUS_FINISHED;
        $this->completed_auctions_count[0]['value'] = Auction::getAuctionCount($this->startDate, $this->endDate, Auction::STATUS_FINISHED);
        $this->completed_auctions_count[1]['name'] = Auction::STATUS_FAILED;
        $this->completed_auctions_count[1]['value'] = Auction::getAuctionCount($this->startDate, $this->endDate, Auction::STATUS_FAILED);

        $orig_days = $this->days;
        $step = $this->calculateStep($this->tab, $this->days, $this->endDate);
        $segments = $this->days / $step;
        if ($this->tab == 'personal' && $this->days > 10) {
            $this->days++;
            $this->days = $this->days - $step == $orig_days ? $orig_days : $this->days;
            $segments = floor($this->days / $step) + 1;
        }

        $finished_auctions = array_fill(0, $segments, 0);
        $placed_bids = array_fill(0, $segments, 0);
        $bid_amount = array_fill(0, $segments, 0);
        $winning_bid_amount = array_fill(0, $segments, 0);

        for ($i = 0; $i < $this->days; $i++) {
            $day = $this->startDate->copy()->addDays($i)->toDateString();
            $index = floor($i / $step);
            $finished_auctions[$index] += $finished_auctions_raw[$day] ?? 0;
            $placed_bids[$index] += $placed_bids_raw[$day] ?? 0;
            $bid_amount[$index] += $bid_amount_raw[$day] ?? 0;
            $winning_bid_amount[$index] += $winning_bid_amount_raw[$day] ?? 0;
        }

        for ($i = 0; $i < $this->days; $i += $step) {
            $format = $this->startDate->format('Y') == $this->endDate->format('Y') ? 'd.m' : 'd.m.y';
            $day = $this->startDate->copy()->addDays($i)->format($format);
            $this->keys[] = $day;
        }

        $this->finished_auctions = $finished_auctions;
        $this->placed_bids = $placed_bids;
        $this->bid_amount = $bid_amount;
        $this->winning_bid_amount = $winning_bid_amount;
    }

    private function calculateAnalytics() {
        
        if ($this->isOneDay()) {
            $this->calculateAnalyticsForDay();
        } else if ($this->isRangeOfDays()) {        
            $this->calculateAnalyticsForRange();
        }
    }

    public function getFinishedAuctions() 
    {
        return $this->finished_auctions;
    }

    public function getPlacedBids() 
    {
        return $this->placed_bids;
    }

    public function getBidAmount() 
    {
        return $this->bid_amount;
    }

    public function getWinningBidAmount() 
    {
        return $this->winning_bid_amount;
    }

    public function getCompletedAuctionsCount() 
    {
        return $this->completed_auctions_count;
    }

    public function getKeys()
    {
        return $this->keys;
    }
}