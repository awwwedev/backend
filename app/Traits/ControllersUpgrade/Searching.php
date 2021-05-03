<?php


namespace App\Traits\ControllersUpgrade;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait Searching
{
    /**
     * @param Builder $builder
     * @param Request $request
     * @return Builder
     */
    public function attachSearching(Builder $builder, Request $request): Builder
    {
        if ($request->has('searchField') and $request->has('searchValue')) {
            $builder->whereRaw("$request->searchField regexp :searchValue", [ ':searchValue' => $request->searchValue ]);
        }

        return $builder;
    }
}
