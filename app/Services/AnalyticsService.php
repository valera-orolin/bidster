<?php

namespace App\Services;

use Carbon\Carbon;

class AnalyticsService
{
    private $tab;
    private $from;
    private $to;

    private $day;
    private $startDate;
    private $endDate;
    private $days;
    private $tariffsList;

    private $ordersCount;
    private $activeDriversCount;
    private $ridesCost;
    private $ordersByTariffCount;
    private $keys;
    private $detailed_keys;

    /*
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
        $this->detailed_keys = null;
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
        $hoursInDay = count($this->keys);

        $this->ordersCount = $this->initializeOrdersCount($hoursInDay);
        $this->activeDriversCount = array_fill(0, $hoursInDay, 0);
        $this->ridesCost = $this->initializeRidesCost($hoursInDay);
        $this->ordersByTariffCount = $this->calculateOrdersByTariffByHour($hoursInDay);

        $this->fillDataByHour($this->ordersCount['card'], Order::getOrdersByHour($this->day, 'count', Order::TYPE_CARD, Order::STATUS_COMPLETED));
        $this->fillDataByHour($this->ordersCount['card_canceled'], Order::getOrdersByHour($this->day, 'count', Order::TYPE_CARD, Order::STATUS_CANCELED));
        $this->fillDataByHour($this->ordersCount['cash'], Order::getOrdersByHour($this->day, 'count', Order::TYPE_CASH, Order::STATUS_COMPLETED));
        $this->fillDataByHour($this->ordersCount['cash_canceled'], Order::getOrdersByHour($this->day, 'count', Order::TYPE_CASH, Order::STATUS_CANCELED));
        $this->fillDataByHour($this->activeDriversCount, Driver::getActiveDriversByHour($this->day));
        $this->fillDataByHour($this->ridesCost['card'], Order::getOrdersByHour($this->day, 'total_cost', Order::TYPE_CARD, Order::STATUS_COMPLETED));
        $this->fillDataByHour($this->ridesCost['cash'], Order::getOrdersByHour($this->day, 'total_cost', Order::TYPE_CASH, Order::STATUS_COMPLETED));
    }

    private function initializeOrdersCount($length)
    {
        return [
            'card' => array_fill(0, $length, 0),
            'card_canceled' => array_fill(0, $length, 0),
            'cash' => array_fill(0, $length, 0),
            'cash_canceled' => array_fill(0, $length, 0),
        ];
    }

    private function initializeRidesCost($length)
    {
        return [
            'card' => array_fill(0, $length, 0),
            'cash' => array_fill(0, $length, 0),
        ];
    }

    private function fillDataByHour(&$data, $orders)
    {
        foreach ($orders as $hour => $count) {
            $data[$hour] = $count;
        }
    }

    private function calculateOrdersByTariffByHour($length)
    {
        $tariffs = [];

        foreach ($this->tariffsList as $tariff) {
            $ordersForCurrentTariff = Order::getOrdersByHour($this->day, 'count', null, Order::STATUS_COMPLETED, $tariff->id);
            $tariffs[$tariff->title] = array_fill(0, $length, 0);
            $this->fillDataByHour($tariffs[$tariff->title], $ordersForCurrentTariff);
        }

        return $tariffs;
    }

    private function calculateAnalyticsForRange()
    {
        $orders = [];
        $drivers = [];
        $rides = [];
        $tariffs = [];

        $ordersCard = Order::getOrdersByDay($this->startDate, $this->endDate, 'count', Order::TYPE_CARD, Order::STATUS_COMPLETED, null);
        $ordersCardCanceled = Order::getOrdersByDay($this->startDate, $this->endDate, 'count', Order::TYPE_CARD, Order::STATUS_CANCELED, null);
        $ordersCash = Order::getOrdersByDay($this->startDate, $this->endDate, 'count', Order::TYPE_CASH, Order::STATUS_COMPLETED, null);
        $ordersCashCanceled = Order::getOrdersByDay($this->startDate, $this->endDate, 'count', Order::TYPE_CASH, Order::STATUS_CANCELED, null);

        $driversActive = Driver::getActiveDriversByDay($this->startDate, $this->endDate);

        $ridesCard = Order::getOrdersByDay($this->startDate, $this->endDate, 'total_cost', Order::TYPE_CARD, Order::STATUS_COMPLETED, null);
        $ridesCash = Order::getOrdersByDay($this->startDate, $this->endDate, 'total_cost', Order::TYPE_CASH, Order::STATUS_COMPLETED, null);
    
        $orig_days = $this->days;
        $step = $this->calculateStep($this->tab, $this->days, $this->endDate);
        $segments = $this->days / $step;
        if ($this->tab == 'personal' && $this->days > 10) {
            $this->days++;
            $this->days = $this->days - $step == $orig_days ? $orig_days : $this->days;
            $segments = floor($this->days / $step) + 1;
        }

        $orders['card'] = array_fill(0, $segments, 0);
        $orders['card_canceled'] = array_fill(0, $segments, 0);
        $orders['cash'] = array_fill(0, $segments, 0);
        $orders['cash_canceled'] = array_fill(0, $segments, 0);
        $drivers = array_fill(0, $segments, 0);
        $rides['card'] = array_fill(0, $segments, 0);
        $rides['cash'] = array_fill(0, $segments, 0);

        for ($i = 0; $i < $this->days; $i++) {
            $day = $this->startDate->copy()->addDays($i)->toDateString();
            $index = floor($i / $step);
            $orders['card'][$index] += $ordersCard[$day] ?? 0;
            $orders['card_canceled'][$index] += $ordersCardCanceled[$day] ?? 0;
            $orders['cash'][$index] += $ordersCash[$day] ?? 0;
            $orders['cash_canceled'][$index] += $ordersCashCanceled[$day] ?? 0;
            $drivers[$index] += $driversActive[$day] ?? 0;
            $rides['card'][$index] += $ridesCard[$day] ?? 0;
            $rides['cash'][$index] += $ridesCash[$day] ?? 0;
        }

        foreach ($this->tariffsList as $tariff) {
            $ordersForCurrentTariff = Order::getOrdersByDay($this->startDate, $this->endDate, 'count', null, Order::STATUS_COMPLETED, $tariff->id);
            $tariffs[$tariff->title] = array_fill(0, $segments, 0);
            for ($i = 0; $i < $this->days; $i++) {
                $day = $this->startDate->copy()->addDays($i)->toDateString();
                $index = floor($i / $step);
                $tariffs[$tariff->title][$index] += $ordersForCurrentTariff[$day] ?? 0;
            }
        }

        for ($i = 0; $i < $this->days; $i += $step) {
            $format = $this->startDate->format('Y') == $this->endDate->format('Y') ? 'd.m' : 'd.m.y';
            $day = $this->startDate->copy()->addDays($i)->format($format);
            $this->keys[] = $day;

            if ($i < $this->days - 1 && $step > 1) {
                $second_day = $this->startDate->copy()->addDays($i + $step)->subDays(1)->format($format);
                $this->detailed_keys[] = $day . ' - ' . $second_day;
            } else {
                $this->detailed_keys[] = $day;
            }
        }

        $this->ordersCount = $orders;
        $this->activeDriversCount = $drivers;
        $this->ridesCost = $rides;
        $this->ordersByTariffCount = $tariffs;
    }

    private function calculateAnalytics() {
        
        if ($this->isOneDay()) {
            $this->calculateAnalyticsForDay();
        } else if ($this->isRangeOfDays()) {        
            $this->calculateAnalyticsForRange();
        }

    }

    public function getOrdersCount() 
    {
        return $this->ordersCount;
    }

    public function getActiveDriversCount()
    {
        return $this->activeDriversCount;
    }

    public function getRidesCost()
    {
        return $this->ridesCost;
    }

    public function getOrdersByTariffCount()
    {
        return $this->ordersByTariffCount;
    }

    public function getKeys()
    {
        return $this->keys;
    }

    public function getDetailedKeys()
    {
        return $this->detailed_keys;
    }
    */
}