<?php

namespace App\Http\Controllers;

use App\Http\Resources\RealtyCollection;
use App\Http\Resources\RealtyResource;
use App\Models\Realty;
use App\Models\RealtyEquipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class RealtyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return RealtyCollection
     */
    public function index(Request $request)
    {
        $realty = $this->filter($request);
        $request->has('perPage') ? $perPage = $request->get('perPage') : $perPage = 10;

        if ($request->has('sortBy')) {
            $realty->orderBy($request->sortBy, $request->sortType ?? 'desc');
        } else {
            $realty->orderBy('created_at', 'desc');
        }

        if ($request->has('searchField') and $request->has('searchValue')) {
            $realty->where($request->searchField, 'like', "%$request->searchValue%");
        }

        return new RealtyCollection($realty->paginate($perPage));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return RealtyResource
     */
    public function store(Request $request)
    {
        $realty = Realty::make($request->only(['name','discount_sum', 'description', 'price', 'area', 'price_per_metr', 'type_id', 'longitude', 'latitude']));
        $realty->img_path = '/storage/' . $request->file('img_path')->store('images/realty', 'public');
        $realty->photo = collect($request->file('photo'))->map(function ($file) {
            return '/storage/' . $file->store('images/realty', 'public');
        });
        $realty->user_id = Auth::user()->id;

        $realty->save();

        if ($request->has('equipments')) {
            $realty->equipments()->attach($request->equipments);
        }

        return RealtyResource::make($realty);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Realty  $realty
     * @return RealtyResource
     */
    public function show(Realty $realty)
    {
        return RealtyResource::make($realty);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Realty  $realty
     * @return RealtyResource
     */
    public function update(Request $request, Realty $realty)
    {
        $realty = $realty->fill($request->only(['name','discount_sum', 'description', 'price', 'photo', 'area', 'price_per_metr', 'type_id', 'longitude', 'latitude']));
        $realtyEquipIds = collect($realty->equipments()->get())->map(function ($model) { return $model->id; });

        if (!$request->has('photo')) {
            $realty->photo = [];
        }
        if ($request->has('equipments')) {
            $requestEquip = collect($request->equipments);

            if ($realty->getOriginal('type_id') !== (int) $realty->type_id) {
                $realty->equipments()->detach($realtyEquipIds);
                $realty->equipments()->attach($requestEquip);
            } else {
                if ($requestEquip->diff($realtyEquipIds)->count() !== 0) {
                    $equipmentsToDelete = $realtyEquipIds->diff($requestEquip);
                    $equipmentsToAdd = $requestEquip->diff($realtyEquipIds);

                    $realty->equipments()->detach($equipmentsToDelete);
                    $realty->equipments()->attach($equipmentsToAdd);
                }
            }
        } else {
            $realty->equipments()->detach($realtyEquipIds);
        }
        try {
            // TODO: добавить удалдение фоток
            if ($request->hasFile('img_path')) {
                $realty->img_path = '/storage/' . $request->file('img_path')->store('images/realty', 'public');
            }

            // TODO: добавить удалдение фоток
            if ($request->hasFile('newPhoto')) {
                $realty->photo = collect($request->file('newPhoto'))->map(function ($file) {
                    return '/storage/' . $file->store('images/realty', 'public');
                })->merge($realty->photo);
            }
            if(!$realty->update()){
                throw new \Exception('Cannot save property');
            }
            return RealtyResource::make($realty);
        } catch (\Exception $e) {
            return ['error'=>$e->getMessage()];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Realty  $realty
     * @return bool
     */
    public function destroy(Realty $realty)
    {
        // TODO: добавить удалдение фоток
        return $realty->delete();
    }

    public function destroyMultiple(Request $request): int
    {
        // TODO: добавить удалдение фоток
        RealtyEquipment::whereHas('realty', function ($q) use ($request) {
            $q->whereIn('id', $request->id);
        })->delete();
        return Realty::destroy($request->id);
    }

    public function minMax(Request $request): array
    {
        $realty = $this->filter($request);
        return [
            'pricePerMetrMin' => $realty->min('price_per_metr'),
            'pricePerMetrMax' => $realty->max('price_per_metr'),
            'priceMin' => $realty->min('price'),
            'priceMax' => $realty->max('price'),
            'areaMin' => $realty->min('area'),
            'areaMax' => $realty->max('area')
        ];
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    public function count(Request $request)
    {
        $realty = $this->filter($request);
        return ['amount' => $realty->count()];
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function mapRealty(Request $request)
    {
        $realty = $this->filter($request);
        return $realty->get();
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function filter(Request $request)
    {
        $realty = Realty::whereNotNull('description');

        if ($request->has('equipments')) {
            $realty->whereHas('equipments', function($query) use ($request) {
                $query->whereIn('equipment.id', $request->equipments);
            });
        }
        if ($request->has('types')) {
            $realty->whereIn('type_id', $request->get('types'));
        }
        if ($request->has('exceptedId')) {
            $realty->whereNotIn('id', $request->get('exceptedId'));
        }
        if ($request->has('areaMin')) {
            $realty->where('area', '>=', $request->get('areaMin'));
        }
        if ($request->has('discount')) {
            $realty->where('discount_sum', '>=', 0);
        }
        if ($request->has('areaMax')) {
            $realty->where('area', '<=', $request->get('areaMax'));
        }
        if ($request->has('latitudeMin')) {
            $realty->where('latitude', '>=', $request->get('latitudeMin'));
        }
        if ($request->has('latitudeMax')) {
            $realty->where('latitude', '<=', $request->get('latitudeMax'));
        }
        if ($request->has('longitudeMin')) {
            $realty->where('longitude', '<=', $request->get('longitudeMin'));
        }
        if ($request->has('longitudeMax')) {
            $realty->where('longitude',  '>=', $request->get('longitudeMax'));
        }
        if ($request->has('priceMin')) {
            $realty->where('price', '>=', $request->get('priceMin'));
        }
        if ($request->has('priceMax')) {
            $realty->where('price', '<=', $request->get('priceMax'));
        }
        if ($request->has('pricePerMetrMin')) {
            $realty->where('price_per_metr', '>=', $request->get('pricePerMetrMin'));
        }
        if ($request->has('pricePerMetrMax')) {
            $realty->where('price_per_metr', '<=', $request->get('pricePerMetrMax'));
        }
        return $realty;
    }
}
