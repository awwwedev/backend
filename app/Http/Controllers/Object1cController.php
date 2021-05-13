<?php

namespace App\Http\Controllers;

use App\Models\Object1c;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Object1cController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param User $user
     *
     * @return mixed
     */
    public function index()
    {
        return User::where('id',Auth::user()->getAuthIdentifier())->first()->object1cs;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $object1c=new Object1c();
        $object1c->name=$request->input('name');
        $object1c->id_1c=$request->input('id_1c');
        if (!User::where('id', $request->input('user_id'))->get()){
            new Response(['error'=>'User not exist']);
    }
        $object1c->user_id=$request->input('user_id');
        $object1c->save();
        return $object1c;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Object1c  $object1c
     * @return \Illuminate\Http\Response
     */
    public function show(Object1c $object1c)
    {
        return $object1c ;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Object1c  $object1c
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Object1c $object1c)
    {
        $object1c->name=$request->input('name');
        $object1c->id_1c=$request->input('id_1c');
        if (User::where('id', $request->input('user_id'))->get()){
            $object1c->user_id=$request->input('user_id');
        }
        $object1c->save();
        return $object1c;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Object1c  $object1c
     * @return \Illuminate\Http\Response
     */
    public function destroy(Object1c $object1c)
    {
        $object1c->delete();
    }

    public function getAll(){
        return [Object1c::all()];
    }
}
