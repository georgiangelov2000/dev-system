<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Builder;

class DashboardHelper
{
    static function getCounts($query)
    {
        return $query->count();
    }

    static function getCountsByStatus(Builder $query, array $configNames,  string $status)
    {
        return $query
            ->get()
            ->groupBy($status)
            ->map(function ($item) {
                return ['count' => $item->count()];
            })
            ->mapWithKeys(function ($item, $status) use ($configNames) {
                return [$configNames[$status] => $item];
            })->toArray();
    }

    static function getCountsByPackage(Builder $query, array $configNames)
    {
        $monthCounts = 0;

        $byStatus = $query
            ->get()
            ->groupBy(function ($packageOrder) {
                return optional($packageOrder->package)->delievery_method;
            })
            ->mapWithKeys(function ($group, $key) use ($configNames, &$monthCounts) {
                $count = count($group);
                $monthCounts += $count;
                return [$configNames[$key] => $group->count()];
            })->toArray();

        $result = [
            'by_status' => $byStatus,
            'counts' => $monthCounts,
        ];

        return $result;
    }
}
