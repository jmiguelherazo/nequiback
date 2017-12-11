<?php

namespace App\Http\Controllers;

use App\Goal;
use App\Pocket;
use App\UserGoal;
use App\Util\NequiClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

    public function me()
    {
        return Auth::user();
    }

    public function goals(Request $req)
    {
        if ($req->query('category')) {
            return Goal::where('category_id', $req->query('category'))->get();
        }

        return Goal::all();
    }

    public function myGoals(Request $req)
    {
        return Auth::user()->goals;
    }

    public function myPockets(Request $req)
    {
        return Auth::user()->pockets;
    }

    public function updatePocket2(Request $req)
    {
        DB::beginTransaction();

        try {

            $pocket0 = Auth::user()->pockets->where('type_id', 0)->first();

            if ($pocket0) {
                if ($pocket0->money < $req->input('new_money')) {
                    abort(403, 'INSUFFICIENT_MONEY');
                } else {
                    $pocket0->money -= $req->input('new_money');
                    $pocket0->save();
                }

            } else {
                abort(400);
            }

            $pocket2 = Auth::user()->pockets->where('type_id', 2)->first();

            if (!$pocket2) {
                $pocket2          = new Pocket();
                $pocket2->user_id = Auth::id();
                $pocket2->type_id = 2;
                $pocket2->money   = 0;
                $pocket2->name    = "Guardadito";
                $pocket2->save();
            }

            $pocket2->money += $req->input('new_money');
            $pocket2->save();

            // VERIFY GOALS
            $goals = array();

            // Verify Goal # 1
            $goal1 = Auth::user()->goals->where('id', 1)->first();

            if (!$goal1) {
                $new_goal          = new UserGoal();
                $new_goal->user_id = Auth::id();
                $new_goal->goal_id = 1;
                $new_goal->save();

                $goals[] = Goal::find(1);
            }

            // Verify Goal # 2
            if ($pocket2->money > 10000) {
                $goal2 = Auth::user()->goals->where('id', 2)->first();

                if (!$goal2) {
                    $new_goal          = new UserGoal();
                    $new_goal->user_id = Auth::id();
                    $new_goal->goal_id = 2;
                    $new_goal->save();

                    $goals[] = Goal::find(2);
                }
            }

            DB::commit();

            return response()->json(array(
                'pocket'         => $pocket2,
                'goals_adquired' => $goals,
            ));

        } catch (Exception $e) {
            DB::rollback();
            abort(500);
        }
    }
}
