<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Traits\ControllersUpgrade\Searching;
use App\Traits\ControllersUpgrade\Sorting;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use Sorting;
    use Searching;

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return Builder[]|Collection
     */
    public function index(Request $request)
    {
        $builder = $this->attachSorting(User::query(), $request);

        return $builder->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return User|Response
     */
    public function store(Request $request)
    {
        $user = new User();
        $user->name = $request->input('name');
        $user->phone = $request->input('phone');
        $user->email = $request->input('email');
        $user->role_id = $request->input('role_id', Role::where('role', Role::TENANT)->first());
        $user->password = Hash::make($request->input('password'));
        if ($user->save()) {
            return $user;
        } else {
            return new Response(json_encode($user->errors()->all()), 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param User $user
     * @return User
     */
    public function show(User $user): User
    {
        return $user;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param User $user
     * @return User|Response
     */
    public function update(Request $request, User $user)
    {
        $user->name = $request->input('name');
        $user->phone = $request->input('phone');
        $user->email = $request->input('email');
        $user->role_id = $request->input('role_id', Role::where('role', Role::TENANT)->first());
        $user->password = Hash::make($request->input('password'));
        $user->save();
        if ($user->save()) {
            return $user;
        } else {
            return new Response(json_encode($user->errors()->all()), 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     * @return Response
     * @throws Exception
     */
    public function destroy(User $user): Response
    {
        $user->delete();
        return new Response();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return Authenticatable
     */
    public function byToken(): Authenticatable
    {
        return Auth::user();
    }

}
