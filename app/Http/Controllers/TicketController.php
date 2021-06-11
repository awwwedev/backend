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

    public function count(Request $request): int
    {
        $builder = $this->attachFilterByFields(['user_id', 'status'], $request, Ticket::query());

        return $builder->count();
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
    public function show(Ticket $ticket)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  \App\Models\Ticket  $ticket
     * @return Response
     */
    public function update(Request $request, Ticket $ticket)
    {
        //
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
