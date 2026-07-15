<?php

namespace App\Modules\Admin\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use App\Modules\Hr\Models\Employee;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;


class User extends Authenticatable
{
    use HasFactory;
    use HasRoles, Notifiable;
    use SoftDeletes;



    protected $guard_name = 'web';


    protected $table = 'users';



    public $timestamps = true;


    protected $fillable = [
        'name', 'email', 'status', 'password', 'company_id'
    ];

    protected $guarded = [

    ];

    protected $casts = [
        'email_verified_at' => 'datetime'
    ];

    protected $attributes = [

    ];

    protected $dispatchesEvents = [

    ];

    /**
     * Validation rules for the model.
     */
    protected static $rules = [

    ];

    /**
     * Custom validation messages.
     */
    protected static $messages = [

    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

    }

    /**
     * Validate the model instance.
     */
    public function validate()
    {
        $validator = Validator::make($this->attributesToArray(), static::$rules, static::$messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }

    /**
     * Save the model to the database with validation.
     */
    public function save(array $options = [])
    {
        $this->validate();
        return parent::save($options);
    }

    public function company()
    {
        return $this->belongsTo(\App\Modules\Admin\Models\Company::class, 'company_id', 'id');
    }

    public function employee()
    {
        return $this->hasOne(\App\Modules\Hr\Models\Employee::class, 'user_id', 'id');
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return \App\Modules\Admin\Database\Factories\UserFactory::new();
    }
}
