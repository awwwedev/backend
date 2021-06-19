<?php

namespace App\Http\Controllers;

use App\Models\Request as RequestModel;
use App\Traits\ControllersUpgrade\Searching;
use App\Traits\ControllersUpgrade\Sorting;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class RequestController extends Controller
{
    use Sorting;
    use Searching;

    public function index(Request $request)
    {
        $builder = $this->attachSorting(RequestModel::query(), $request);
        $builder = $this->attachSearching($builder, $request);
        $perPage = $request->get('perPage') ?? 10;

        return $builder->paginate($perPage);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $mailRecord = new RequestModel();
        $mailRecord->message = $request->input('message');
        $mailRecord->phone = $request->input('phone');
        $mailRecord->email = $request->input('email');
        $mailRecord->realtie_id = $request->input('realtyId');
        $mailRecord->new = true;
        if (!$mailRecord->save()) {
            return ['error' => 'cannot save message'];
        }

        return RequestModel::query()->find($mailRecord->id);

       /* try {


            ini_set("SMTP", env('SMTP'));
            ini_set("sendmail_from", env('ADMIN_EMAIL'));
            $message = $request->input('message');
            $headers = "From:" . env('ADMIN_EMAIL');

            $res = mail("luthenkoev@gmail.com", "Новая заявка на аренду недвижимости", $message, $headers);

            return ['result' => $res ? "Заявка отправлена успешно" : "Заявка не отправлена"];
        }catch (\Exception $e){
            return ['error' => $e->getMessage()];
        }*/
    }


    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param RequestModel $modelInst
     * @return RequestModel
     */
    public function show(Request $request)
    {
        return RequestModel::query()->find($request->id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param RequestModel $request
     * @return Response
     */
    public function update(Request $request)
    {
        $mailRecord = RequestModel::query()->find($request->id);
        $mailRecord->new = (int) $request->new;
        if (!$mailRecord->save()) {
            return ['error' => 'cannot save message'];
        }

        return $mailRecord;
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param RequestModel $request
     * @return Response
     */
    public function destroy(Request $request)
    {
        //
    }
}
