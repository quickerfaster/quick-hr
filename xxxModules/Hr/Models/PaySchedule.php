<?php

namespace App\Modules\Hr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;


use Illuminate\Database\Eloquent\Model;


class PaySchedule extends Model 
{
    use HasFactory;
    
    

    

    protected $table = 'pay_schedules';
    
    
    
    
    

    protected $fillable = [
        'name', 'frequency', 'next_pay_date', 'is_active'
    ];

    protected $guarded = [
        
    ];

    protected $casts = [
        'next_pay_date' => 'date',
        'is_active' => 'boolean'
    ];

    protected $attributes = [
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

    

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return \App\Modules\Hr\Database\Factories\PayScheduleFactory::new();
    }
}