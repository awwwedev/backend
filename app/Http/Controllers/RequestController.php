<?php

namespace App\Http\Controllers;

use App\Models\Request as Model;
use Illuminate\Http\Request;

class RequestController extends Controller
{

    public function index(Request $request)
    {
        try {
            $mailRecord = new Model();
            $mailRecord->message = $request->get('message');
            $mailRecord->phone = ($request->has('phone') ? $request->get('phone') : null);
            $mailRecord->email = ($request->has('email') ? $request->get('email') : null);
            $mailRecord->new = true;
            if (!$mailRecord->save()) {
                return ['error' => 'cannot save message'];
            }

            ini_set("SMTP", env('SMTP'));
            ini_set("sendmail_from", env('ADMIN_EMAIL'));
            $message = $request->get('message');
            $headers = "From:" . env('ADMIN_EMAIL');

            $res = mail("luthenkoev@gmail.com", "Новая заявка на аренду недвижимости", $message, $headers);
            echo ($res) ? "Почта отправлена" : "Почта не отправлена";

            return ['sended' => $res];
        }catch (\Exception $e){
            return ['error'=>$e->getMessage()];
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        //
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        //
    }
}
