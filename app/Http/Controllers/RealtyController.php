<?php

namespace App\Http\Controllers;

use App\Exceptions\RelationDeleteException;
use App\Http\Resources\RealtyCollection;
use App\Http\Resources\RealtyResource;
use App\Models\Realty;
use App\Models\RealtyEquipment;
use App\Traits\ControllersUpgrade\Sorting;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;


class RealtyController extends Controller
{
    use Sorting;
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return RealtyCollection
     */
    public function index(Request $request): RealtyCollection
    {
        $builder = $this->filter($request);
        $builder = $this->attachSorting($builder, $request);
        $perPage = $request->perPage ?? 10;

        if ($request->has('searchField') and $request->has('searchValue')) {
            $builder->where($request->searchField, 'like', "%$request->searchValue%");
        }

        return new RealtyCollection($builder->paginate($perPage));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return RealtyResource
     */
    public function store(Request $request): RealtyResource
    {
        $realty = Realty::make($request->only(['name', 'description', 'price', 'area', 'price_per_metr', 'type_id', 'longitude', 'latitude']));
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
     * @param Realty $realty
     * @return RealtyResource
     */
    public function show(Realty $realty): RealtyResource
    {
        return RealtyResource::make($realty);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Realty $realty
     * @return RealtyResource|Response
     */
    public function update(Request $request, Realty $realty)
    {
        $realty = $realty->fill($request->only(['name', 'description', 'price', 'photo', 'area', 'price_per_metr', 'type_id', 'longitude', 'latitude']));
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
            if ($request->hasFile('img_path')) {
                $realty->img_path = '/storage/' . $request->file('img_path')->store('images/realty', 'public');
            }

            if ($request->hasFile('newPhoto')) {
                $realty->photo = collect($request->file('newPhoto'))->map(function ($file) {
                    return '/storage/' . $file->store('images/realty', 'public');
                })->merge($realty->photo);
            }
            if(!$realty->update()){
                throw new Exception('Cannot save property');
            }
            return RealtyResource::make($realty);
        } catch (Exception $e) {
            return response(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Realty $realty
     * @return bool
     * @throws Exception
     */
    public function destroy(Realty $realty): bool
    {
        try {
            $res = $realty->delete();
            RealtyEquipment::where('realty_id', null)->delete();
        } catch (QueryException $ex) {
            throw new RelationDeleteException($realty->id);
        }

        return $res;
    }

    /**
     * @param Request $request
     * @return int
     * @throws RelationDeleteException | Exception
     */
    public function destroyMultiple(Request $request): int
    {
        try {
            $res = Realty::select(['id', 'photo', 'img_path'])->whereIn('id', $request->id)->get()
                ->each(function (Realty $model) {
                    $model->delete();
                })->count();
            RealtyEquipment::where('realty_id', null)->delete();
        } catch (QueryException $ex) {
            throw new RelationDeleteException($request->id[0]);
        }

        return $res;
    }

    /**
     * @param Request $request
     * @return array
     */
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
     * @return Builder
     */
    public function filter(Request $request): Builder
    {
        $realty = Realty::query();

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
