<?php


namespace App\Traits\ControllersUpgrade;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait Sorting
{
    /**
     * @param Builder $builder
     * @param Request $request
     * @return Builder
     */
    private function attachSorting(Builder $builder, Request $request): Builder
    {
        if ($request->has('sortBy')) {
            $builder->orderBy($request->sortBy, $request->sortType ?? 'desc');
        } else {
            $builder->orderBy('created_at', 'desc');
        }

        return $builder;
    }
}
