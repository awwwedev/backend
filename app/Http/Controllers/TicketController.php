<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Traits\ControllersUpgrade\Filtering;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TicketController extends Controller
{
    use Filtering;

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function index(Request $request): LengthAwarePaginator
    {
        $builder = Ticket::query();
        $perPage = $request->get('perPage', 10);

        $this->attachFilterByFields(['user_id', 'status'], $request, $builder);

        return $builder->paginate($perPage);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Ticket  $ticket
     * @return Response
     */
    public function show(Request $request, Ticket $ticket)
    {
        $builder = Ticket::query();

        if ($request->user_id)
            $builder->where('user_id', $request->user_id);
        if ($request->id)
            $builder->where('id', $request->id);

        return $builder->first();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  \App\Models\Ticket  $ticket
     * @return Ticket
     */
    public function update(Request $request, Ticket $ticket)
    {
        $ticket->status = $request->status;

        $ticket->update();

        return $ticket;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Ticket  $ticket
     * @return Response
     */
    public function destroy(Ticket $ticket)
    {
        //
    }
}
