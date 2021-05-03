<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Slide;
use App\Traits\ControllersUpgrade\Searching;
use App\Traits\ControllersUpgrade\Sorting;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;


class ContactController extends Controller
{
    use Sorting;

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Contact[]|Collection|JsonResponse
     */
    public function index(Request $request)
    {
        $builder = Contact::query();
        $builder = $this->attachSorting($builder, $request);

        return $builder->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Contact
     */
    public function store(Request $request): Contact
    {
        $contact = Contact::make($request->only(['value', 'type', 'is_rent_department']));
        $contact->user_id = Auth::user()->id;
        $contact->header = $request->header;
        $contact->save();

        return $contact;
    }

    /**
     * Display the specified resource.
     *
     * @param Contact $contact
     * @return Contact
     */
    public function show(Contact $contact)
    {
        return $contact;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Contact $contact
     * @return Contact
     */
    public function update(Request $request, Contact $contact)
    {
        $contact->fill($request->only(['value', 'type', 'is_rent_department']));
        $contact->header = $request->header;
        $contact->update();

        return $contact;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Contact $contact
     * @return bool
     */
    public function destroy(Contact $contact)
    {
        return $contact->delete();
    }


    /**
     * @param Request $request
     * @return mixed
     */
    public function destroyMultiple(Request $request)
    {
        return Contact::whereIn('id', $request->id)->delete();
    }
}
