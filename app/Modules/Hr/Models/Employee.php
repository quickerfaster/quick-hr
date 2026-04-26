<?php

namespace App\Modules\Hr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Modules\Hr\Models\EmployeePosition;
use App\Modules\Hr\Models\EmployeeJobHistory;
use App\Modules\Hr\Models\EmployeeProfile;
use App\Modules\Admin\Models\Company;
use App\Modules\Hr\Models\Document;
use App\Modules\Hr\Models\EmployeeWorkPattern;
use App\Models\User;

use Illuminate\Database\Eloquent\Model;


class Employee extends Model 
{
    use HasFactory;
    
    

    

    protected $table = 'employees';
    
    
    
    
    

    protected $fillable = [
        'company_id', 'employee_number', 'first_name', 'last_name', 'phone', 'hire_date', 'email', 'user_id'
    ];

    protected $guarded = [
        
    ];

    protected $casts = [
        'hire_date' => 'date'
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

    public function employeePosition()
    {
        return $this->hasOne(\App\Modules\Hr\Models\EmployeePosition::class, 'employee_id', 'id');
    }

    public function jobHistory()
    {
        return $this->hasMany(\App\Modules\Hr\Models\EmployeeJobHistory::class, 'employee_id', 'id');
    }

    public function employeeProfile()
    {
        return $this->hasOne(\App\Modules\Hr\Models\EmployeeProfile::class, 'employee_id', 'id');
    }

    public function company()
    {
        return $this->belongsTo(\App\Modules\Admin\Models\Company::class, 'company_id', 'id');
    }

    public function documents()
    {
        return $this->hasMany(\App\Modules\Hr\Models\Document::class, 'employee_id', 'id');
    }

    public function employeeWorkPatterns()
    {
        return $this->hasMany(\App\Modules\Hr\Models\EmployeeWorkPattern::class, 'employee_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return \App\Modules\Hr\Database\Factories\EmployeeFactory::new();
    }
}