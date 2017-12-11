<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $appends = ['points', 'main_role'];

    private $roles = array(
        1 => 'Guardián',
        2 => 'Aventurero',
        3 => 'Guerrero',
    );

    private $levels = array(
        0    => 'aprendiz',
        250  => 'experimentado',
        1000 => 'avanzado',
    );

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getMainRoleAttribute()
    {
        $main = count($this->points) > 0 ? $this->points[0] : null;

        if ($main) {
            $role = $this->roles[$main->category_id];
            $role .= ' ';

            $level = '';
            foreach ($this->levels as $key => $value) {
                if ($main->exp > $key) {
                    $level = $value;
                }

            }

            return $role . $level;
        }

        return null;
    }

    public function getPointsAttribute()
    {
        $sql = "
            SELECT category_id, SUM(points) AS exp
            FROM goals AS G
            INNER JOIN user_goals AS UG
            ON G.id = UG.goal_id
            WHERE UG.user_id = ?
            GROUP BY category_id
            ORDER BY 2 DESC
        ";

        return DB::select($sql, [$this->id]);
    }

    public function goals()
    {
        return $this->belongsToMany('App\Goal', 'user_goals');
    }

    public function pockets()
    {
        return $this->hasMany('App\Pocket');
    }
}
