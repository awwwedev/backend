<?php

namespace App\Http\Controllers;

use App\Exceptions\RelationDeleteException;
use App\Models\RealtyType;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RealtyTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return RealtyType[]|\Illuminate\Database\Eloquent\Collection|Response
     */
    public function index()
    {
        return RealtyType::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Response
     */
    public function store(Request $request)
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
    public function show(RealtyType $realtyType)
    {
        return $realtyType;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param RealtyType $realtyType
     * @return RealtyType
     */
    public function update(Request $request, RealtyType $realtyType)
    {
        $realtyType->fill($request->only(['name']));

        // TODO: добавить удалдение фотоки
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
     * @throws RelationDeleteException
     */
    public function destroy(RealtyType $realtyType)
    {
        // TODO: добавить удалдение фотоки
        try {
            return $realtyType->delete();
        } catch (QueryException $ex) {
            throw new RelationDeleteException($realtyType->id);
        }
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws RelationDeleteException
     */
    public function destroyMultiple(Request $request)
    {
        // TODO: добавить удалдение фотоки
        try {
            return RealtyType::whereIn('id', $request->id)->delete();
        } catch (QueryException $ex) {
            throw new RelationDeleteException($request->id[0]);
        }
    }
}
