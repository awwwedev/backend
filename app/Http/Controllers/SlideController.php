<?php

namespace App\Http\Controllers;

use App\Models\Slide;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class SlideController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Slide[]|Collection|Response
     */
    public function index()
    {
        return Slide::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request): Response
    {
        $slide = Slide::make($request->only(['header', 'content']));
        $slide->image = '/storage/' . $request->file('image')->store('images/slide', 'public');
        $slide->user_id = Auth::user()->id;
        $slide->save();

        return $slide;
    }

    /**
     * Display the specified resource.
     *
     * @param Slide $slide
     * @return Slide
     */
    public function show(Slide $slide): Slide
    {
        return $slide;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Slide $slide
     * @return Slide
     */
    public function update(Request $request, Slide $slide)
    {
        $slide->fill($request->only(['header', 'content']));

        if ($request->hasFile('image')) {
            // TODO: добавить удалдение фотоки
            $slide->image = '/storage/' . $request->file('image')->store('images/slide', 'public');
        }
        $slide->update();

        return $slide;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Slide $slide
     * @return bool
     * @throws Exception
     */
    public function destroy(Slide $slide)
    {
        // TODO: добавить удалдение фотоки
        return $slide->delete();
    }

    public function destroyMultiple(Request $request)
    {
        // TODO: добавить удалдение фотоки
        return Slide::whereIn('id', $request->id)->delete();
    }
}
