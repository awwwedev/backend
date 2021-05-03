<?php

namespace App\Http\Controllers;

use App\Exceptions\RelationDeleteException;
use App\Models\RealtyType;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RealtyTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return RealtyType[]|Collection|Response
     */
    public function index()
    {
        return RealtyType::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return RealtyType
     */
    public function store(Request $request): RealtyType
    {
        $realtyType = RealtyType::make($request->only(['name']));
        $realtyType->img_path = '/storage/' . $request->file('img_path')->store('images/realtyType', 'public');

        $realtyType->save();

        return $realtyType;
    }

    /**
     * Display the specified resource.
     *
     * @param RealtyType $realtyType
     * @return RealtyType
     */
    public function show(RealtyType $realtyType): RealtyType
    {
        return $realtyType;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param RealtyType $realtyType
     * @return RealtyType
     */
    public function update(Request $request, RealtyType $realtyType): RealtyType
    {
        $realtyType->fill($request->only(['name']));

        if ($request->hasFile('img_path')) {
            $realtyType->img_path = '/storage/' . $request->file('img_path')->store('images/realtyType', 'public');
        }

        $realtyType->update();

        return $realtyType;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param RealtyType $realtyType
     * @return bool
     * @throws RelationDeleteException | Exception
     */
    public function destroy(RealtyType $realtyType): bool
    {
        try {
            return $realtyType->delete();
        } catch (QueryException $ex) {
            throw new RelationDeleteException($realtyType->id);
        }
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws RelationDeleteException | Exception
     */
    public function destroyMultiple(Request $request)
    {
        try {
            return RealtyType::select(['id', 'img_path'])->whereIn('id', $request->id)->get()
                ->each(function (RealtyType $model) {
                    $model->delete();
                })->count();
        } catch (QueryException $ex) {
            throw new RelationDeleteException($request->id[0]);
        }
    }
}
