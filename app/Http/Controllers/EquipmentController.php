<?php

namespace App\Http\Controllers;

use App\Exceptions\RelationDeleteException;
use App\Http\Resources\EquipmentResource;
use App\Models\Contact;
use App\Models\Equipment;
use App\Traits\ControllersUpgrade\Sorting;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;


class EquipmentController extends Controller
{
    use Sorting;
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $builder = $this->attachSorting(Equipment::query(), $request);

        if ($request->has('realtyTypeId')) {
            $builder->whereHas('realtyType', function($query) use ($request) {
                $query->where('realty_type_id', $request->realtyTypeId);
            });
        }

        return EquipmentResource::collection($builder->get());
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return EquipmentResource
     */
    public function store(Request $request): EquipmentResource
    {
        $equip = Equipment::make($request->only(['name', 'display_name', 'realty_type_id']));
        $equip->save();

        return EquipmentResource::make($equip);
    }

    /**
     * Display the specified resource.
     *
     * @param Equipment $equipment
     * @return EquipmentResource
     */
    public function show(Equipment $equipment)
    {
        return EquipmentResource::make($equipment);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Equipment $equipment
     * @return EquipmentResource
     */
    public function update(Request $request, Equipment $equipment): EquipmentResource
    {
        $equip = $equipment->fill($request->only(['name', 'display_name', 'realty_type_id']));
        $equip->update();

        return EquipmentResource::make($equipment);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Equipment $equipment
     * @return bool
     * @throws \Exception
     */
    public function destroy(Equipment $equipment): bool
    {
        try {
            return $equipment->delete();
        } catch (QueryException $ex) {
            throw new RelationDeleteException($equipment->id);
        }
    }


    /**
     * @param Request $request
     * @return mixed
     * @throws RelationDeleteException
     */
    public function destroyMultiple(Request $request)
    {
        try {
            return Equipment::whereIn('id', $request->id)->delete();
        } catch (QueryException $ex) {
            throw new RelationDeleteException($request->id[0]);
        }
    }
}
