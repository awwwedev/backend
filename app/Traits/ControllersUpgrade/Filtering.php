<?php


namespace App\Traits\ControllersUpgrade;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait Filtering
{
    public function attachFilterByFields(array $fieldNames, Request $request, Builder $builder): Builder
    {
        collect($fieldNames)->each(function (string $field) use ($request, $builder) {
            if ($request->has($field)) $builder->when($field, $request->get($field));
        });

        return $builder;
    }
}
