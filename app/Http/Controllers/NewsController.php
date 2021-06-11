<?php

namespace App\Http\Controllers;

use App\Http\Resources\NewsCollection;
use App\Models\News;
use App\Traits\ControllersUpgrade\Searching;
use App\Traits\ControllersUpgrade\Sorting;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class NewsController extends Controller
{
    use Sorting;
    use Searching;

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return NewsCollection
     */
    public function index(Request $request): NewsCollection
    {
        $builder = $this->attachSorting(News::query(), $request);
        $builder = $this->attachSearching($builder, $request);
        $perPage = $request->get('perPage') ?? 10;

        return new NewsCollection($builder->paginate($perPage));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $news = News::make($request->only(['header', 'content', 'short_description']));
        $news->photo = '/storage/' . $request->file('photo')->store('images/news', 'public');
        $news->user_id = Auth::user()->id;
        $news->save();

        return $news;
    }

    /**
     * Display the specified resource.
     *
     * @param News $news
     * @return News
     */
    public function show(News $news)
    {
        return $news;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param News $news
     * @return News
     */
    public function update(Request $request, News $news): News
    {
        $news->fill($request->only(['header', 'content', 'short_description']));

        if ($request->hasFile('photo')) {
            $news->photo = '/storage/' . $request->file('photo')->store('images/news', 'public');
        }
        $news->update();

        return $news;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param News $news
     * @return bool
     * @throws Exception
     */
    public function destroy(News $news): bool
    {
        return $news->delete();
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws Exception
     */
    public function destroyMultiple(Request $request)
    {
        return News::select(['id', 'photo'])->whereIn('id', $request->id)->get()
            ->each(function (News $model) {
                $model->delete();
            })->count();
    }
}
