<?php

namespace App\Modules\Hr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Modules\Hr\Models\Employee;

use Illuminate\Database\Eloquent\Model;


class EmployeeGroup extends Model 
{
    use HasFactory;
    
    

    

    protected $table = 'employee_groups';
    
    
    
    public $timestamps = true;
    

    protected $fillable = [
        'name', 'code', 'description', 'group_type', 'dynamic_rules', 'is_active'
    ];

    protected $guarded = [
        
    ];

    protected $casts = [
        'dynamic_rules' => 'array',
        'is_active' => 'boolean',
        'created_by' => 'integer',
        'updated_by' => 'integer'
    ];

    protected $attributes = [
        'group_type' => 'manual',
        'is_active' => true
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

    public function employees()
    {
        return $this->belongsToMany(\App\Modules\Hr\Models\Employee::class, 'employee_employee_group', 'employee_group_id', 'employee_id', 'id', 'id');
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return \App\Modules\Hr\Database\Factories\EmployeeGroupFactory::new();
    }
}