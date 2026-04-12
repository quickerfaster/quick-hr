<?php

namespace App\Modules\Hr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Modules\Hr\Models\Shift;

use Illuminate\Database\Eloquent\Model;


class WorkPattern extends Model 
{
    use HasFactory;
    
    

    

    protected $table = 'work_patterns';
    
    
    
    
    

    protected $fillable = [
        'name', 'code', 'description', 'shift_id', 'applicable_days', 'override_start_time', 'override_end_time', 'pattern_type', 'rotation_weeks', 'effective_date', 'end_date', 'is_active', 'is_default'
    ];

    protected $guarded = [
        
    ];

    protected $casts = [
        'rotation_weeks' => 'integer',
        'effective_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'assigned_employee_count' => 'integer',
        'last_used_date' => 'date',
        'created_from_template_id' => 'integer'
    ];

    protected $attributes = [
        'applicable_days' => array (
  0 => 1,
  1 => 2,
  2 => 3,
  3 => 4,
  4 => 5,
),
        'pattern_type' => 'recurring',
        'rotation_weeks' => 2,
        'is_active' => true,
        'is_default' => false,
        'assigned_employee_count' => 0
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

    public function shift()
    {
        return $this->belongsTo(\App\Modules\Hr\Models\Shift::class, 'shift_id', 'id');
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return \App\Modules\Hr\Database\Factories\WorkPatternFactory::new();
    }
}