<?php

namespace App\Models;

use Watson\Validating\ValidatingTrait;

/**
 * Abstract base model for all custom models of the application.
 * This model is used as a layer between the custom models representing a database table and the generic eloquent model.
 *
 * @author Stefan Herndler
 * @since 1.0.0
 * @class Model
 * @package App\Models
 */
abstract class Model extends \Illuminate\Database\Eloquent\Model {

	use ValidatingTrait;

	/**
	 * Collection of all validation rules which will be applied to the model attributes upon saving the model (create and update).
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @var array
	 */
	protected $rules = [];
}
