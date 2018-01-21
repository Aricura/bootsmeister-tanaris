<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;

/**
 * Model representing a single authenticatable user.
 *
 * @author Stefan Herndler
 * @since 1.0.0
 * @class User
 * @package App\Models
 * @property integer id
 * @property string email
 * @property string password
 * @property string name
 * @property string remember_token
 * @property \Carbon\Carbon created_at
 * @property \Carbon\Carbon updated_at
 */
class User extends Model implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract {

	use Notifiable, Authenticatable, Authorizable, CanResetPassword;

	/**
	 * The table associated with the model.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @var array
	 */
	protected $fillable = ['email', 'password', 'name'];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @var array
	 */
	protected $hidden = ['password', 'remember_token'];

	/**
	 * The attributes that should be cast to native types.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @var array
	 */
	protected $casts = [
		'email' => 'string',
		'password' => 'string',
		'name' => 'string'
	];
}