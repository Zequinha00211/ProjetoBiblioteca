<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $table = 'users';
    protected $connection = 'mysql';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'email',
        'password',
        'privilegio_id'
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function cmsPrivilege()
    {
        return $this->hasOne('App\Models\Privilegio', 'ID', 'privilegio_id');
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function scopeUsuarioAutenticado($query)
    {
        $idUsuario = null;
        try {
            if ($user = \JWTAuth::parseToken()->authenticate()) {
                $idUsuario = $user->id;
            }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            $idUsuario = null;
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            $idUsuario = null;
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
            $idUsuario = null;
        } catch (\Exception $e) {
            $idUsuario = null;
        }

        if ($idUsuario != '') {
            return $query->where('id', $idUsuario);
        } else {
            return $query->whereNull('id');
        }
    }
}
