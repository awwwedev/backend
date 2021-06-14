<?php

namespace App\Http\Controllers;

use App\Models\Object1c;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Object1cController extends Controller
{
    const ALL=0;
    const WATER=1;
    const SERVICES=2;
    const ELECTRICITY=3;
    const RENT=4;
    /**
     * Display a listing of the resource.
     *
     * @param User $user
     *
     * @return mixed
     */
    public function index(Request $request)
    {
        $id = $request->input('user_id', Auth::user()->getAuthIdentifier());

        return User::where('id', $id)->first()->object1cs ?? [];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $object1c = new Object1c();
        $object1c->name = $request->input('name');
        $object1c->id_1c = $request->input('id_1c');
        if (!User::where('id', $request->input('user_id'))->get()) {
            new Response(['error' => 'User not exist']);
        }
        $object1c->user_id = $request->input('user_id');
        $object1c->save();

        return $object1c;
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Object1c $object1c
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Object1c $object1c)
    {
        return $object1c;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Object1c     $object1c
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Object1c $object1c)
    {
        $object1c->name = $request->input('name');
        $object1c->id_1c = $request->input('id_1c');
        if (User::where('id', $request->input('user_id'))->get()) {
            $object1c->user_id = $request->input('user_id');
        }
        $object1c->save();

        return $object1c;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Object1c $object1c
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Object1c $object1c)
    {
        return $object1c->delete();
    }

    public function getAll()
    {
        return [Object1c::all()];
    }

    public function getContract(Request $request)
    {
        try {
            $object_id = $request->get('object_id');
            $log = new Logger('info');
            $log->pushHandler(new StreamHandler(__DIR__.'/../../../logs/info', Logger::INFO));
            $log->info(
                'getContract info',
                [
                    'user' => Auth::user()->getAuthIdentifier(),
                    'object_id' => $object_id
                ]
            );
            $user = User::where('id', Auth::user()->getAuthIdentifier())->first();
            $object = $user->object1cs()->where('id_1c', $object_id)->first();
            if (!$object) {
                throw new \Exception('Not found Object');
            }
            $data=http_build_query([
                                       'object_id' => $object_id,
                                       'user_id' => $user->id
                                   ]);
            $ref = env('HOST1CGETDOCUMENTS').'?'.$data;
            return ['href'=>$ref];
        } catch (\Exception $e) {
            $log = new Logger('error');
            $log->pushHandler(new StreamHandler(__DIR__.'/../../../logs/error', Logger::ERROR));
            $log->error(
                'getContract',
                [
                    'error' => $e->getMessage(),
                    'user' => $user,
                    'object' => $object
                ]
            );
            return [
                'error' => $e->getMessage(),
                'user' => $user,
                'object' => $object
            ];
        }
    }

    public function getBill(Request $request)
    {
        try {
            $object_id = $request->get('object_id');
            $type = $request->get('type');
            $billId = $request->get('bill_id');
            $log = new Logger('info');
            $log->pushHandler(new StreamHandler(__DIR__.'/../../../logs/info', Logger::INFO));
            $log->info(
                'get bill info',
                [
                    'user' => Auth::user()->getAuthIdentifier(),
                    'object_id' => $object_id,
                    'bill_id'=>$billId
                ]
            );
            $user = User::where('id', Auth::user()->getAuthIdentifier())->first();
            $object = $user->object1cs()->where('id_1c', $object_id)->first();
            if (!$object) {
                throw new \Exception('Not found Object');
            }
            $data=http_build_query([
                                       'object_id' => $object_id,
                                       'user_id' => $user->id,
                                       'type'=>$type,
                                       'bill_id'=>$billId
                                   ]);
            $ref = env('HOST1CGETDOCUMENTS').'?'.$data;
            return ['href'=>$ref];
        } catch (\Exception $e) {
            $log = new Logger('error');
            $log->pushHandler(new StreamHandler(__DIR__.'/../../../logs/error', Logger::ERROR));
            $log->error(
                'getContract',
                [
                    'error' => $e->getMessage(),
                    'user' => $user,
                    'object' => $object,
                    'bill_id'=>$billId
                ]
            );
            return [
                'error' => $e->getMessage(),
                'user' => $user,
                'object' => $object,
                'bill_id'=>$billId
            ];
        }
    }

    public function getBills(Request $request)
    {
        try {
            $object_id = $request->get('object_id');
            $type = $request->get('type');
            $dateFrom = $request->get('date_from');
            $dateTo = $request->get('date_to');
            $log = new Logger('info');
            $log->pushHandler(new StreamHandler(__DIR__.'/../../../logs/info', Logger::INFO));
            $log->info(
                'get bills info',
                [
                    'user' => Auth::user()->getAuthIdentifier(),
                    'object_id' => $object_id
                ]
            );
            $user = User::where('id', Auth::user()->getAuthIdentifier())->first();
            $object = $user->object1cs()->where('id_1c', $object_id)->first();
            if (!$object) {
                throw new \Exception('Not found Object');
            }
            $document = Http::get(
                env('HOST1CGETBILLS'),
                [
                    'object_id' => $object_id,
                    'user_id' => $user->id,
                    'type'=>$type,
                    'date_from'=>$dateFrom,
                    'date_to'=>$dateTo,
                ]
            );
            $response=new Response($document);
            return $response;
        } catch (\Exception $e) {
            $log = new Logger('error');
            $log->pushHandler(new StreamHandler(__DIR__.'/../../../logs/error', Logger::ERROR));
            $log->error(
                'getContract',
                [
                    'error' => $e->getMessage(),
                    'user' => $user,
                    'object' => $object
                ]
            );
            return [
                'error' => $e->getMessage(),
                'user' => $user,
                'object' => $object
            ];
        }
    }


    public function getDebts(Request $request)
    {
        try {
            $object_id = $request->get('object_id');
            $type = $request->get('type');
            $dateFrom = $request->get('date_from');
            $dateTo = $request->get('date_to');
            $log = new Logger('info');
            $log->pushHandler(new StreamHandler(__DIR__.'/../../../logs/info', Logger::INFO));
            $log->info(
                'get debts',
                [
                    'user' => Auth::user()->getAuthIdentifier(),
                    'object_id' => $object_id,
                ]
            );
            $user = User::where('id', Auth::user()->getAuthIdentifier())->first();
            $object = $user->object1cs()->where('id_1c', $object_id)->first();
            if (!$object) {
                throw new \Exception('Not found Object');
            }
            $document = Http::get(
                env('HOST1CGETDEBTS'),
                [
                    'object_id' => $object_id,
                    'user_id' => $user->id,
                    'type'=>$type,
                    'date_from'=>$dateFrom,
                    'date_to'=>$dateTo,
                ]
            );
            $response=new Response($document);
            return $response;
        } catch (\Exception $e) {
            $log = new Logger('error');
            $log->pushHandler(new StreamHandler(__DIR__.'/../../../logs/error', Logger::ERROR));
            $log->error(
                'debts',
                [
                    'error' => $e->getMessage(),
                    'user' => $user,
                    'object' => $object
                ]
            );
            return [
                'error' => $e->getMessage(),
                'user' => $user,
                'object' => $object
            ];
        }
    }


    public function getCounters(Request $request)
    {
        try {
            $object_id = $request->get('object_id');
            $type = $request->get('type');
            $log = new Logger('info');
            $log->pushHandler(new StreamHandler(__DIR__.'/../../../logs/info', Logger::INFO));
            $log->info(
                'get counters',
                [
                    'user' => Auth::user()->getAuthIdentifier(),
                    'object_id' => $object_id,
                ]
            );
            $user = User::where('id', Auth::user()->getAuthIdentifier())->first();
            $object = $user->object1cs()->where('id_1c', $object_id)->first();
            if (!$object) {
                throw new \Exception('Not found Object');
            }
            $result = Http::get(
                env('HOST1CGETCOUNTERS'),
                [
                    'object_id' => $object_id,
                    'user_id' => $user->id,
                    'type'=>$type,
                ]
            );
            $response=new Response($result);
           return $response;
        } catch (\Exception $e) {
            $log = new Logger('error');
            $log->pushHandler(new StreamHandler(__DIR__.'/../../../logs/error', Logger::ERROR));
            $log->error(
                'getCounters',
                [
                    'error' => $e->getMessage(),
                    'user' => $user,
                    'object' => $object
                ]
            );
            return [
                'error' => $e->getMessage(),
                'user' => $user,
                'object' => $object
            ];
        }
    }

    public function getStatistics(Request $request)
    {
        try {
            $object_id = $request->get('object_id');
            $type = $request->get('type');
            $log = new Logger('info');
            $log->pushHandler(new StreamHandler(__DIR__.'/../../../logs/info', Logger::INFO));
            $log->info(
                'get statistics',
                [
                    'user' => Auth::user()->getAuthIdentifier(),
                    'object_id' => $object_id,
                    'ip'=>$request->getClientIp()
                ]
            );
            $user = User::where('id', Auth::user()->getAuthIdentifier())->first();
            $object = $user->object1cs()->where('id_1c', $object_id)->first();
            if (!$object) {
                throw new \Exception('Not found Object');
            }
            $result = Http::get(
                env('HOST1CGETSTATISTICS'),
                [
                    'object_id' => $object_id,
                    'user_id' => $user->id,
                    'type'=>$type,
                ]
            );
            $response=new Response($result);
            return $response;
        } catch (\Exception $e) {
            $log = new Logger('error');
            $log->pushHandler(new StreamHandler(__DIR__.'/../../../logs/error', Logger::ERROR));
            $log->error(
                'getCounters',
                [
                    'error' => $e->getMessage(),
                    'user' => $user,
                    'object' => $object,
                    'ip'=>$request->getClientIp()
                ]
            );
            return [
                'error' => $e->getMessage(),
                'user' => $user,
                'object' => $object
            ];
        }
    }
}
