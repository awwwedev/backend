<?php

namespace App\Http\Controllers;

use App\Http\Resources\TicketMessageCollection;
use App\Models\Role;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketMessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return TicketMessageCollection
     */
    public function index(Request $request)
    {
        $builder = null;

        if ($request->has('ticket_user_id')) {
            $user = User::query()->find($request->ticket_user_id);
            $builder = $user->ticket()->with('messages')->first()->messages()->orderBy('created_at', 'asc');
        }
        return TicketMessageCollection::make($builder->get());
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $newMessage = TicketMessage::make([ 'message' => $request->message, 'user_id' => Auth::user()->id ]);
        $user = User::query()->find($request->ticket_user_id);
        $ticket = $user->ticket;
        $ticket->status = Auth::user()->role->role === Role::ADMIN ? Ticket::STATE_WAITING : Ticket::STATE_NEW;

        $ticket->messages()->save($newMessage);
        $ticket->save();

        return $newMessage;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TicketMessage  $ticketMessage
     * @return \Illuminate\Http\Response
     */
    public function show(TicketMessage $ticketMessage)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TicketMessage  $ticketMessage
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TicketMessage $ticketMessage)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TicketMessage  $ticketMessage
     * @return \Illuminate\Http\Response
     */
    public function destroy(TicketMessage $ticketMessage)
    {
        //
    }
}
