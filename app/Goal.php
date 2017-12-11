<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Goal extends Model
{
    //

    protected $appends = ['adquired_by_me', 'adquired_at'];

    public function getAdquiredByMeAttribute()
    {
        if (Auth::id()) {
            $userGoal = UserGoal::where('user_id', Auth::id())->where('goal_id', $this->id)->first();
        } else {
            return null;
        }

        return $userGoal != null;
    }

    public function getAdquiredAtAttribute()
    {
        if (Auth::id()) {
            $userGoal = UserGoal::where('user_id', Auth::id())->where('goal_id', $this->id)->first();
        } else {
            return null;
        }

        return $userGoal != null ? $userGoal->created_at->format('Y-m-d H:i:s') : null;
    }

    public function users()
    {
        return $this->belongsToMany('App\User', 'user_goals');
    }
}
