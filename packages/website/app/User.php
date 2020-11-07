<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class User extends \Eloquent implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'password', 'role',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Check the user has specific role.
     *
     * @param string|array $role
     * @return bool
     */
    public function own($role)
    {
        if (! $this->exists) {
            return false;
        } elseif ('admin' === $this->getAttribute('role')) {
            return true;
        } elseif (is_array($role)) {
            return in_array($this->getAttribute('role'), $role, true);
        }

        return $role === $this->getAttribute('role');
    }
}
