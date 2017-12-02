<?php

namespace App\Http\Controllers;

use App\Goal;
use App\Util\NequiClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function nequiTest(Request $req)
    {
        $nequi = new NequiClient();

        $response = json_decode($nequi->getPoints($req->query('latitude'), $req->query('longitude')));

        if ($response->ResponseMessage->ResponseHeader->Status->StatusCode == 0) {
            return response()->json($response->ResponseMessage->ResponseBody->any->getNequiPointsRS);
        } else {
            return abort(404);
        }

    }

    public function goals(Request $req)
    {
        return Goal::all();
    }

    public function myGoals(Request $req)
    {
        return Auth::user()->goals;
    }
}
