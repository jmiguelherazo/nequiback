<?php

namespace App\Http\Controllers;

use App\Util\NequiClient;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }

    public function nequiTest(Request $req)
    {
        $nequi = new NequiClient();
        // return $nequi->validateClient("12345", "3195414070", "0");
        return response()->json(json_decode($nequi->getPoints($req->query('latitude'), $req->query('longitude'))));
    }
}
